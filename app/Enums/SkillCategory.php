<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

/**
 * Groups skills into sections on the home page.
 *
 * @author Grager D'souza <gragerdsouza@gmail.com>
 */
enum SkillCategory: string implements HasLabel
{
    case Backend = 'backend';
    case Frontend = 'frontend';
    case DevOps = 'devops';
    case Testing = 'testing';
    case Data = 'data';
    case Tooling = 'tooling';

    /**
     * Human-readable label shown in the UI.
     *
     * @return string The label for this category.
     */
    public function getLabel(): string
    {
        return match ($this) {
            self::Backend => 'Backend',
            self::Frontend => 'Frontend',
            self::DevOps => 'DevOps & Cloud',
            self::Testing => 'Testing & Quality',
            self::Data => 'Data & Storage',
            self::Tooling => 'Tooling',
        };
    }
}
