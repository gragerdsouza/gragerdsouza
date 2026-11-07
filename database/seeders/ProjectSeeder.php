<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Project;
use Illuminate\Database\Seeder;

/**
 * Seeds the portfolio projects. Idempotent: a project is only created when its slug
 * does not exist yet, so edits made in the admin are never overwritten.
 *
 * @author Grager D'souza <gragerdsouza@gmail.com>
 */
class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeder.
     *
     * @return void Nothing is returned.
     */
    public function run(): void
    {
        foreach ($this->projects() as $project) {
            Project::query()->firstOrCreate(
                ['slug' => $project['slug']],
                [...$project, 'published_at' => now()],
            );
        }
    }

    /**
     * The projects to seed, in display order.
     *
     * @return list<array{slug: string, title: string, summary: string, body: string, tech_stack: list<string>, url: string|null, repository_url: string|null, is_featured: bool, sort_order: int}> Project attributes keyed by column.
     */
    private function projects(): array
    {
        return [
            [
                'slug' => 'digital-wallet-passes',
                'title' => 'Digital Wallet Passes',
                'summary' => 'End-to-end digital pass solution for Apple Wallet and Google Wallet on iOS and Android — designed, built and delivered as a solo project.',
                'body' => <<<'MD'
                ## Overview

                A complete digital pass solution that puts passes straight into the phone wallet people already use: **Apple Wallet** on iOS and **Google Wallet** on Android.

                ## My role

                Sole developer, from first design to release:

                - Designed the pass layouts and the data behind them
                - Built pass generation and issuing for both Apple Wallet and Google Wallet
                - Handled the platform-specific requirements of each wallet so passes install cleanly on iOS and Android
                - Tested on real devices and shipped to production

                ## Outcome

                Completed and delivered as a solo project.
                MD,
                'tech_stack' => ['PHP', 'Laravel', 'Apple Wallet', 'Google Wallet', 'iOS', 'Android'],
                'url' => null,
                'repository_url' => null,
                'is_featured' => true,
                'sort_order' => 0,
            ],
            [
                'slug' => 'vehicle-data-service',
                'title' => 'Vehicle Data Service',
                'summary' => 'Provider-based API that looks up a vehicle by registration (VRM) and returns images, valuation and recall data from external providers — designed and built as a solo project.',
                'body' => <<<'MD'
                ## Overview

                A single, secure API for vehicle data. Send a **VRM** (vehicle registration mark) and get back:

                - **Images** of the vehicle
                - **Valuation**
                - **Recall** information

                Each data type comes from an external provider, but consumers only ever see one consistent response model.

                ## Architecture

                - **Provider interface pattern:** business logic depends on a common contract, never on a specific vendor
                - **Adapter-based implementations:** each provider is wrapped in its own adapter; the first integration was a vehicle image provider
                - **Provider registration and configuration:** new providers plug in with minimal code changes and no refactoring of the service
                - **Consistent response schema** (e.g. a `VehicleImage` model) regardless of which provider answered

                ## Quality & security

                - Authentication and authorisation enforced on every endpoint
                - Caching for repeated lookups, cutting provider calls and response times
                - Unit and integration tests across the service and adapters

                ## Why it matters

                - Decouples external providers from business logic
                - New providers can be added without refactoring the service
                - Easier to maintain and extend

                ## My role

                Sole developer: architecture, provider integrations, API, caching and tests.

                ## Outcome

                Completed and delivered as a solo project.
                MD,
                'tech_stack' => ['PHP', 'Laravel', 'REST API', 'Adapter pattern', 'Provider integrations', 'Caching', 'Unit & integration tests'],
                'url' => null,
                'repository_url' => null,
                'is_featured' => true,
                'sort_order' => 1,
            ],
            [
                'slug' => 'whatsapp-hub',
                'title' => 'WhatsApp Hub (in development)',
                'summary' => 'Twilio-powered REST API for two-way WhatsApp customer communications — contacts, conversation threads and delivery tracking — running on Laravel Cloud. Currently in development.',
                'body' => <<<'MD'
                ## Overview

                A REST API that lets a business talk to customers on WhatsApp in both directions. It receives inbound messages through Twilio webhooks, stores contacts and conversations, sends outbound replies and tracks every message through to delivery.

                ## Features

                - **Two-way messaging:** outbound WhatsApp messages via Twilio, replies received through webhooks
                - **Contact management:** contacts keyed by phone number, with flexible metadata per contact
                - **Conversation threads:** messages grouped per contact, with active, archived and closed states
                - **Delivery tracking:** queued, sent, delivered, read and failed statuses synced from Twilio status callbacks

                ## API

                Versioned endpoints under `/api/v1/` for contacts, conversations and messages, plus inbound-message and status webhooks.

                ## Stack & hosting

                Laravel 13 on PHP 8.5, integrated with Twilio (Conversations API and status callbacks), with source on **Bitbucket**, deployed to **Laravel Cloud** through **GitHub Actions**.

                ## Status

                In active development.
                MD,
                'tech_stack' => ['PHP 8.5', 'Laravel 13', 'Twilio', 'WhatsApp', 'REST API', 'Webhooks', 'Bitbucket', 'GitHub Actions', 'Laravel Cloud'],
                'url' => null,
                'repository_url' => null,
                'is_featured' => true,
                'sort_order' => 2,
            ],
            [
                'slug' => 'native-mobile-app',
                'title' => 'Native Mobile App (in development)',
                'summary' => 'A native iOS and Android app built with Laravel and NativePHP for Mobile — using SuperNative to render real SwiftUI and Jetpack Compose UI from Blade. Currently in development.',
                'body' => <<<'MD'
                ## Overview

                A mobile app for **iOS and Android** built entirely in Laravel, using **NativePHP for Mobile**.

                ## Approach

                - **SuperNative architecture:** screens are written as Laravel Blade templates and rendered as truly native UI: **SwiftUI** on iOS and **Jetpack Compose** on Android, rather than a web view
                - **One codebase:** the same Laravel app and Blade templates drive both platforms
                - **Laravel all the way down:** routing, validation, models and tests stay in familiar PHP and Laravel

                ## Status

                In active development.
                MD,
                'tech_stack' => ['PHP', 'Laravel', 'NativePHP', 'SuperNative', 'iOS', 'Android'],
                'url' => null,
                'repository_url' => null,
                'is_featured' => true,
                'sort_order' => 3,
            ],
            [
                'slug' => 'this-portfolio',
                'title' => 'This portfolio',
                'summary' => 'Laravel 13 + Filament admin, 100% test coverage, Dockerised, CI/CD to Laravel Cloud.',
                'body' => <<<'MD'
                ## Highlights

                - Test-driven with Pest, enforced 100% coverage
                - Architecture tests, Larastan and Rector in CI
                - Multi-stage Docker image (FrankenPHP + Octane)
                - GitHub Actions deploys to Laravel Cloud after green builds
                MD,
                'tech_stack' => ['Laravel', 'Filament', 'Pest', 'Docker', 'GitHub Actions'],
                'url' => null,
                'repository_url' => 'https://github.com/gragerdsouza/gragerdsouza',
                'is_featured' => true,
                'sort_order' => 4,
            ],
        ];
    }
}
