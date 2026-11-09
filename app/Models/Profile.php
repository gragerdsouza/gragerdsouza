<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\ProfileFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

/**
 * The site owner's public profile. There is one row; the admin edits it.
 *
 * @property string $name
 * @property string $headline
 * @property string|null $tagline
 * @property string $bio
 * @property string|null $location
 * @property string $email
 * @property string|null $avatar_path
 * @property string|null $resume_url
 * @property array<string, string>|null $social_links
 * @property bool $available_for_work
 *
 * @author Grager D'souza <gragerdsouza@gmail.com>
 */
#[Fillable(['name', 'headline', 'tagline', 'bio', 'location', 'email', 'avatar_path', 'resume_url', 'social_links', 'available_for_work'])]
class Profile extends Model
{
    /** @use HasFactory<ProfileFactory> */
    use HasFactory;

    /**
     * The saved profile, or an unsaved placeholder so the site renders before setup.
     *
     * @return self The saved profile or a placeholder.
     */
    public static function current(): self
    {
        return self::query()->oldest('id')->first() ?? self::placeholder();
    }

    /**
     * An unsaved profile built from config alone, for pages that must render without the database.
     *
     * @return self The placeholder profile.
     */
    public static function placeholder(): self
    {
        return new self([
            'name' => config()->string('app.name'),
            'headline' => 'Full-stack developer',
            'bio' => 'Profile coming soon.',
            'email' => config()->string('mail.from.address'),
            'social_links' => [],
            'available_for_work' => false,
        ]);
    }

    /**
     * Public URL of the uploaded avatar, if any.
     *
     * @return string|null The URL, or null when no avatar is uploaded.
     */
    public function avatarUrl(): ?string
    {
        return $this->avatar_path === null ? null : Storage::disk('public')->url($this->avatar_path);
    }

    /**
     * Social links that are safe to render: only http(s) URLs, so a stored
     * "javascript:" or "data:" value can never become a clickable link.
     *
     * @return array<string, string> URLs keyed by network name.
     */
    public function socialLinks(): array
    {
        return array_filter(
            $this->social_links ?? [],
            static fn (string $url): bool => preg_match('#^https?://#i', $url) === 1,
        );
    }

    /**
     * Link to the full certifications list on LinkedIn, if a LinkedIn profile is set.
     *
     * @return string|null The LinkedIn certifications URL, or null without a LinkedIn link.
     */
    public function linkedInCertificationsUrl(): ?string
    {
        $linkedIn = $this->socialLinks()['linkedin'] ?? null;

        return $linkedIn === null ? null : rtrim($linkedIn, '/').'/details/certifications/';
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string> Cast types keyed by attribute.
     */
    protected function casts(): array
    {
        return [
            'social_links' => 'array',
            'available_for_work' => 'boolean',
        ];
    }
}
