<?php

declare(strict_types=1);

/**
 * Pest tests: Unit / SkillCategoryTest.
 *
 * @author Grager D'souza <gragerdsouza@gmail.com>
 */

use App\Enums\SkillCategory;

it('has a human label for every case', function (SkillCategory $category, string $label): void {
    expect($category->getLabel())->toBe($label);
})->with([
    [SkillCategory::Backend, 'Backend'],
    [SkillCategory::Frontend, 'Frontend'],
    [SkillCategory::DevOps, 'DevOps & Cloud'],
    [SkillCategory::Testing, 'Testing & Quality'],
    [SkillCategory::Data, 'Data & Storage'],
    [SkillCategory::Tooling, 'Tooling'],
]);
