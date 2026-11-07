<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\SkillCategory;
use Database\Factories\SkillFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * A technology or practice shown in the "Stack" section.
 *
 * @property string $name
 * @property SkillCategory $category
 * @property int $sort_order
 *
 * @author Grager D'souza <gragerdsouza@gmail.com>
 */
#[Fillable(['name', 'category', 'sort_order'])]
class Skill extends Model
{
    /** @use HasFactory<SkillFactory> */
    use HasFactory;

    /**
     * Scope: manual sort order, then alphabetical.
     *
     * @param Builder<self> $query The query being scoped.
     *
     * @return void Nothing is returned.
     */
    #[Scope]
    protected function ordered(Builder $query): void
    {
        $query->orderBy('sort_order')->orderBy('name');
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string> Cast types keyed by attribute.
     */
    protected function casts(): array
    {
        return [
            'category' => SkillCategory::class,
            'sort_order' => 'integer',
        ];
    }
}
