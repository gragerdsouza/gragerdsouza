<?php

declare(strict_types=1);

namespace App\Filament\Resources\Profiles\Schemas;

use Closure;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

/**
 * Form schema used to create and edit profile records.
 *
 * @author Grager D'souza <gragerdsouza@gmail.com>
 */
final class ProfileForm
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
                Section::make('About you')
                    ->columnSpan(2)
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')->required()->maxLength(255),
                        TextInput::make('headline')->required()->maxLength(255),
                        TextInput::make('tagline')->maxLength(255)->columnSpanFull(),
                        Textarea::make('bio')->required()->rows(6)->columnSpanFull(),
                    ]),
                Section::make('Contact & links')
                    ->columnSpan(1)
                    ->schema([
                        FileUpload::make('avatar_path')
                            ->label('Avatar')
                            ->image()
                            ->avatar()
                            ->disk('public')
                            ->directory('avatars')
                            ->maxSize(2048),
                        TextInput::make('email')->email()->required()->maxLength(255),
                        TextInput::make('location')->maxLength(255),
                        TextInput::make('resume_url')->label('Résumé URL')->url()->maxLength(255),
                        Toggle::make('available_for_work'),
                        KeyValue::make('social_links')
                            ->keyLabel('Network')
                            ->valueLabel('URL')
                            ->keyPlaceholder('github')
                            ->valuePlaceholder('https://github.com/you')
                            ->rules([static fn (): Closure => self::httpUrlsOnly(...)]),
                    ]),
            ]);
    }

    /**
     * Validation rule: every social link must be a full http(s) URL, so no
     * "javascript:" or "data:" link can be saved.
     *
     * @param string $attribute The field being validated.
     * @param mixed $value The submitted key/value pairs.
     * @param Closure(string): mixed $fail Callback that records a validation error.
     *
     * @return void Nothing is returned.
     */
    public static function httpUrlsOnly(string $attribute, mixed $value, Closure $fail): void
    {
        // The live form holds [{key, value}] rows; programmatic fills pass a plain map.
        foreach ((array) $value as $network => $url) {
            if (is_array($url)) {
                $network = $url['key'] ?? $network;
                $url = $url['value'] ?? null;
            }

            if (! is_string($url) || ! Str::isUrl($url, ['http', 'https'])) {
                $fail(sprintf('The "%s" link must be a full http(s) URL.', is_scalar($network) ? $network : '?'));
            }
        }
    }
}
