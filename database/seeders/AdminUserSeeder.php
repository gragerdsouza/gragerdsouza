<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Creates or promotes the Filament admin user from config/portfolio.php.
 *
 * @author Grager D'souza <gragerdsouza@gmail.com>
 */
class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeder.
     *
     * @return void Nothing is returned.
     */
    public function run(): void
    {
        /** @var array{name: string, email: string, password: string|null} $config */
        $config = config('portfolio.admin');

        $user = User::query()->firstOrNew(['email' => $config['email']]);
        $user->name = $config['name'];
        $user->is_admin = true;

        if ($config['password'] !== null && $config['password'] !== '') {
            $user->password = $config['password'];
        } elseif (! $user->exists) {
            // No password configured: generate one; use "Forgot password" or set ADMIN_PASSWORD and re-seed.
            $user->password = Str::password(32);
            $this->command?->warn('ADMIN_PASSWORD not set — admin created with a random password.');
        }

        $user->save();
    }
}
