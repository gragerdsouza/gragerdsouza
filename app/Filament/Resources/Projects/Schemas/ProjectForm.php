<?php

declare(strict_types=1);

namespace App\Filament\Resources\Projects\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

/**
 * Form schema used to create and edit project records.
 *
 * @author Grager D'souza <gragerdsouza@gmail.com>
 */
final class ProjectForm
{
    /**
     * Apply this configuration to the given component.
     *
     * @param Schema $schema The schema to configure.
     *
     * @return Schema The configured schema.
     */
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                Section::make('Content')
                    ->columnSpan(2)
                    ->columns(2)
                    ->schema([
                        TextInput::make('title')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (Set $set, ?string $state, string $operation): mixed => $operation === 'create' ? $set('slug', Str::slug((string) $state)) : null),
                        TextInput::make('slug')
                            ->required()
                            ->alphaDash()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        Textarea::make('summary')
                            ->required()
                            ->maxLength(500)
                            ->rows(2)
                            ->columnSpanFull(),
                        MarkdownEditor::make('body')
                            ->helperText('Markdown. Raw HTML is stripped when rendered.')
                            ->columnSpanFull(),
                    ]),
                Section::make('Details')
                    ->columnSpan(1)
                    ->schema([
                        TagsInput::make('tech_stack')
                            ->label('Tech stack')
                            ->placeholder('Add a technology'),
                        TextInput::make('url')->label('Live URL')->url()->maxLength(255),
                        TextInput::make('repository_url')->label('Repository URL')->url()->maxLength(255),
                        Toggle::make('is_featured')->label('Featured on home page'),
                        DateTimePicker::make('published_at')
                            ->label('Publish at')
                            ->helperText('Leave empty to keep as a draft.'),
                        TextInput::make('sort_order')->numeric()->minValue(0)->default(0)->required(),
                    ]),
            ]);
    }
}
