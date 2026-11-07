<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\CarbonImmutable;
use Database\Factories\ProjectFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\RouteKey;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * A portfolio project, published on a schedule and optionally featured.
 *
 * @property string $title
 * @property string $slug
 * @property string $summary
 * @property string|null $body
 * @property list<string>|null $tech_stack
 * @property string|null $url
 * @property string|null $repository_url
 * @property bool $is_featured
 * @property CarbonImmutable|null $published_at
 * @property int $sort_order
 *
 * @author Grager D'souza <gragerdsouza@gmail.com>
 */
#[Fillable(['title', 'slug', 'summary', 'body', 'tech_stack', 'url', 'repository_url', 'is_featured', 'published_at', 'sort_order'])]
#[RouteKey('slug')]
class Project extends Model
{
    /** @use HasFactory<ProjectFactory> */
    use HasFactory;

    /**
     * Whether the project is live (publish date set and in the past).
     *
     * @return bool True when the publish date is set and has passed.
     */
    public function isPublished(): bool
    {
        return $this->published_at !== null && $this->published_at->isPast();
    }

    /**
     * Body rendered from Markdown with raw HTML stripped (admin input is still untrusted output).
     *
     * @return string Sanitised HTML.
     */
    public function bodyHtml(): string
    {
        return Str::markdown($this->body ?? '', [
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
        ]);
    }

    /**
     * Scope: only projects whose publish date has passed.
     *
     * @param Builder<self> $query The query being scoped.
     *
     * @return void Nothing is returned.
     */
    #[Scope]
    protected function published(Builder $query): void
    {
        $query->whereNotNull('published_at')->where('published_at', '<=', now());
    }

    /**
     * Scope: only featured projects.
     *
     * @param Builder<self> $query The query being scoped.
     *
     * @return void Nothing is returned.
     */
    #[Scope]
    protected function featured(Builder $query): void
    {
        $query->where('is_featured', true);
    }

    /**
     * Scope: manual sort order first, then newest.
     *
     * @param Builder<self> $query The query being scoped.
     *
     * @return void Nothing is returned.
     */
    #[Scope]
    protected function ordered(Builder $query): void
    {
        $query->orderBy('sort_order')->latest('published_at');
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string> Cast types keyed by attribute.
     */
    protected function casts(): array
    {
        return [
            'tech_stack' => 'array',
            'is_featured' => 'boolean',
            'published_at' => 'immutable_datetime',
            'sort_order' => 'integer',
        ];
    }
}
