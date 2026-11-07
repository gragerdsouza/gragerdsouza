<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\CarbonImmutable;
use Database\Factories\ExperienceFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * A role in the work history timeline.
 *
 * @property string $company
 * @property string $role
 * @property string|null $location
 * @property CarbonImmutable $started_at
 * @property CarbonImmutable|null $ended_at
 * @property string $summary
 *
 * @author Grager D'souza <gragerdsouza@gmail.com>
 */
#[Fillable(['company', 'role', 'location', 'started_at', 'ended_at', 'summary'])]
class Experience extends Model
{
    /** @use HasFactory<ExperienceFactory> */
    use HasFactory;

    /**
     * Whether this is an ongoing role.
     *
     * @return bool True when the role has no end date.
     */
    public function isCurrent(): bool
    {
        return $this->ended_at === null;
    }

    /**
     * Human-readable date range, e.g. "Jan 2022 — Present".
     *
     * @return string Formatted start and end dates.
     */
    public function period(): string
    {
        $end = $this->ended_at?->format('M Y') ?? 'Present';

        return $this->started_at->format('M Y').' — '.$end;
    }

    /**
     * Scope: current roles first, then most recent.
     *
     * @param Builder<self> $query The query being scoped.
     *
     * @return void Nothing is returned.
     */
    #[Scope]
    protected function latestFirst(Builder $query): void
    {
        $query->orderByRaw('ended_at is not null')->latest('started_at');
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string> Cast types keyed by attribute.
     */
    protected function casts(): array
    {
        return [
            'started_at' => 'immutable_date',
            'ended_at' => 'immutable_date',
        ];
    }
}
