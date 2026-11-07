# Testing

> Author: Grager D'souza &lt;gragerdsouza@gmail.com&gt;

This project is test-driven. Every line in `app/` is covered, and CI fails if coverage drops below **100%**.

## Run everything

| What | Docker (recommended) | Inside the container / with local PHP 8.5 |
|---|---|---|
| All quality gates (what CI runs) | `make ci` | `composer ci` |
| Tests, in parallel | `make test` | `composer test` |
| Tests + 100% coverage gate | `make coverage` | `composer test:coverage` |
| Architecture tests only | `docker compose run --rm app composer test:arch` | `composer test:arch` |
| Code style check / fix | `make lint` / `make lint-fix` | `composer lint` / `composer lint:fix` |
| Static analysis (Larastan max) | `make analyse` | `composer analyse` |
| Rector preview / apply | `make rector` / `make rector-fix` | `composer refactor` / `composer refactor:fix` |

Handy Pest flags (append after `docker compose run --rm app vendor/bin/pest`):

```bash
--filter="contact"            # run matching tests
tests/Feature/Admin           # one directory
--parallel                    # run across CPU cores (ParaTest)
--coverage --min=100          # coverage gate (PCOV is in the dev image)
--coverage-html=coverage      # browsable report in ./coverage/index.html
--profile                     # slowest tests
--dirty                       # only tests touched by uncommitted changes
```

## Suites

```
tests/
├── Arch/       architecture rules (layering, strict types, final classes, presets)
├── Unit/       models, scopes, casts, enum labels; fast, with an in-memory DB
└── Feature/
    ├── HomePageTest, ProjectPagesTest, SitemapTest       public site over HTTP
    ├── ContactFormTest                                    validation, honeypot, rate limit, notification
    ├── NewContactMessageNotificationTest                  mail content
    ├── SecurityHeadersTest                                headers + HSTS over https
    ├── SeederTest                                         admin creation, idempotent content
    └── Admin/                                             Filament: access control + CRUD for every resource
```

`tests/Pest.php` applies `RefreshDatabase` to Unit and Feature tests and provides `actingAsAdmin()`.
`phpunit.xml` uses SQLite `:memory:`, the `array` mail/cache/session drivers and a `sync` queue,
so tests need no services. CI also runs the whole suite against **PostgreSQL** for parity.

## Architecture tests

`tests/Arch/ArchTest.php` turns the rules in [ARCHITECTURE.md](ARCHITECTURE.md) into executable checks, for example:

```php
arch('domain does not depend on the admin layer')
    ->expect(['App\Models', 'App\Actions', 'App\Http', 'App\Notifications', 'App\Enums'])
    ->not->toUse('App\Filament');
```

Add a rule whenever you introduce a convention you want to keep.

## TDD loop

1. Write a failing test (`it('…')`) describing the behaviour.
2. `docker compose run --rm app vendor/bin/pest --filter="…"` → red.
3. Write the minimum code → green.
4. Refactor; `make ci` → still green, coverage still 100%.

## Testing Filament

```php
use Livewire\Livewire;

Livewire::test(CreateProject::class)
    ->fillForm(['title' => 'Zero Downtime Deploys'])
    ->assertSchemaStateSet(['slug' => 'zero-downtime-deploys'])
    ->call('create')
    ->assertHasNoFormErrors();

Livewire::test(ListProjects::class)
    ->assertActionVisible(TestAction::make('view')->table($published));
```

## AI-friendly output (Laravel PAO)

[`laravel/pao`](https://github.com/laravel/pao) is installed. When an AI agent such as Claude Code, Cursor, Copilot or Junie
runs Pest, PHPStan or Rector, it prints compact JSON instead of verbose console output, which saves tokens.
`compose.yaml` and the `Makefile` pass the `CLAUDECODE` / `AI_AGENT` variables into the container so this works through Docker too.
Humans get the normal output.

## Coverage driver

The `dev` Docker image ships **PCOV** (`docker/php/dev.ini`). Locally without Docker, install PCOV or Xdebug
(`pecl install pcov`) and run `composer test:coverage`.
