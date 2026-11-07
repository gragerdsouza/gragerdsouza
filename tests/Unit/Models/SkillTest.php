<?php

declare(strict_types=1);

/**
 * Pest tests: Unit / Models / SkillTest.
 *
 * @author Grager D'souza <gragerdsouza@gmail.com>
 */

use App\Enums\SkillCategory;
use App\Models\Skill;

it('casts category to the enum', function (): void {
    $skill = Skill::factory()->create(['category' => 'devops']);

    expect($skill->fresh()?->category)->toBe(SkillCategory::DevOps);
});

it('orders by sort order then name', function (): void {
    $b = Skill::factory()->create(['name' => 'Beta', 'sort_order' => 1]);
    $a = Skill::factory()->create(['name' => 'Alpha', 'sort_order' => 1]);
    $first = Skill::factory()->create(['name' => 'Zulu', 'sort_order' => 0]);

    expect(Skill::query()->ordered()->pluck('id')->all())->toBe([$first->id, $a->id, $b->id]);
});
