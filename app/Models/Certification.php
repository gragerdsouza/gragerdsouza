<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\CarbonImmutable;
use Database\Factories\CertificationFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * A licence or certification shown in the "Certifications" section.
 *
 * @property string $name
 * @property string $issuer
 * @property CarbonImmutable $issued_at
 * @property CarbonImmutable|null $expires_at
 * @property string|null $credential_url
 *
 * @author Grager D'souza <gragerdsouza@gmail.com>
 */
#[Fillable(['name', 'issuer', 'issued_at', 'expires_at', 'credential_url'])]
class Certification extends Model
{
    /** @use HasFactory<CertificationFactory> */
    use HasFactory;

    /**
     * Whether the certification has passed its expiry date.
     *
     * @return bool True when an expiry date is set and is in the past.
     */
    public function isExpired(): bool
    {
        return $this->expires_at !== null && $this->expires_at->isPast();
    }

    /**
     * Human-readable validity, e.g. "Issued Feb 2025" or "Issued Oct 2019 · Expired Oct 2022".
     *
     * @return string The formatted issue (and expiry) date.
     */
    public function validity(): string
    {
        $issued = 'Issued '.$this->issued_at->format('M Y');

        if ($this->expires_at === null) {
            return $issued;
        }

        return $issued.' · '.($this->isExpired() ? 'Expired ' : 'Expires ').$this->expires_at->format('M Y');
    }

    /**
     * Scope: newest first, then alphabetical.
     *
     * @param Builder<self> $query The query being scoped.
     *
     * @return void Nothing is returned.
     */
    #[Scope]
    protected function latestFirst(Builder $query): void
    {
        $query->latest('issued_at')->orderBy('name');
    }

    /**
     * Scope: only certifications without an expiry date, or not yet expired.
     *
     * @param Builder<self> $query The query being scoped.
     *
     * @return void Nothing is returned.
     */
    #[Scope]
    protected function current(Builder $query): void
    {
        $query->where(fn (Builder $q): Builder => $q->whereNull('expires_at')->orWhereDate('expires_at', '>=', now()));
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string> Cast types keyed by attribute.
     */
    protected function casts(): array
    {
        return [
            'issued_at' => 'immutable_date',
            'expires_at' => 'immutable_date',
        ];
    }
}
