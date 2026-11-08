<?php

declare(strict_types=1);

/**
 * Pest tests: Feature / Admin / AdminHardeningTest.
 *
 * Covers the admin IP allowlist and mandatory two-factor authentication.
 *
 * @author Grager D'souza <gragerdsouza@gmail.com>
 */

use App\Models\User;
use Filament\Auth\Pages\Login;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;

describe('IP allowlist', function (): void {
    it('defaults to no allowlist and mandatory 2FA', function (): void {
        $config = configWithEnv('portfolio.php', ['ADMIN_ALLOWED_IPS' => null, 'ADMIN_REQUIRE_MFA' => null, 'ADMIN_PATH' => null]);

        expect($config['admin']['allowed_ips'])->toBe([])
            ->and($config['admin']['require_mfa'])->toBeTrue()
            ->and($config['admin']['path'])->toBe('admin');
    });

    it('lets everyone reach the admin when no allowlist is configured', function (): void {
        config()->set('portfolio.admin.allowed_ips', []);

        $this->get('/admin/login')->assertOk();
    });

    it('hides the admin (404) from IPs outside the allowlist', function (string $url): void {
        config()->set('portfolio.admin.allowed_ips', ['203.0.113.10']);

        $this->get($url)->assertNotFound();
    })->with(['/admin', '/admin/login', '/admin/password-reset/request']);

    it('hides the admin from non-allowlisted IPs even for a signed-in admin', function (): void {
        config()->set('portfolio.admin.allowed_ips', ['203.0.113.10']);
        actingAsAdmin();

        $this->get('/admin')->assertNotFound();
    });

    it('lets allowlisted IPs and CIDR ranges in', function (string $ip): void {
        config()->set('portfolio.admin.allowed_ips', ['203.0.113.10', '198.51.100.0/24', '2001:db8::/32']);

        $this->withServerVariables(['REMOTE_ADDR' => $ip])
            ->get('/admin/login')
            ->assertOk();
    })->with(['203.0.113.10', '198.51.100.77', '2001:db8::1']);

    it('never restricts the public site', function (): void {
        config()->set('portfolio.admin.allowed_ips', ['203.0.113.10']);

        $this->get('/')->assertOk();
    });

    it('parses the allowlist from a comma-separated environment variable', function (): void {
        $config = configWithEnv('portfolio.php', ['ADMIN_ALLOWED_IPS' => ' 203.0.113.10 ,198.51.100.0/24,,']);

        expect($config['admin']['allowed_ips'])->toBe(['203.0.113.10', '198.51.100.0/24']);
    });
});

describe('two-factor authentication', function (): void {
    it('forces an admin without 2FA to set it up before using the panel', function (): void {
        $this->actingAs(User::factory()->admin()->create());

        $this->get('/admin')->assertRedirect('/admin/multi-factor-authentication/set-up');
    });

    it('requires 2FA on the panel by default', function (): void {
        expect(Filament::getPanel('admin')->isMultiFactorAuthenticationRequired())->toBeTrue();
    });

    it('does not sign in with a correct password alone when 2FA is enabled', function (): void {
        $admin = User::factory()->admin()->withAppAuthentication()->create();

        Livewire::test(Login::class)
            ->fillForm(['email' => $admin->email, 'password' => 'password'])
            ->call('authenticate')
            ->assertHasNoFormErrors();

        $this->assertGuest();
    });

    it('stores the 2FA secret and recovery codes encrypted and hidden', function (): void {
        $admin = User::factory()->admin()->withAppAuthentication()->create();
        $admin->saveAppAuthenticationRecoveryCodes(['code-one', 'code-two']);

        $raw = DB::table('users')->where('id', $admin->id)->first();

        expect($raw->app_authentication_secret)->not->toBe($admin->getAppAuthenticationSecret())
            ->and($raw->app_authentication_recovery_codes)->not->toContain('code-one')
            ->and($admin->fresh()?->getAppAuthenticationRecoveryCodes())->toBe(['code-one', 'code-two'])
            ->and($admin->toArray())->not->toHaveKeys(['app_authentication_secret', 'app_authentication_recovery_codes']);
    });
});
