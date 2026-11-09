# Architecture

> Author: Grager D'souza &lt;gragerdsouza@gmail.com&gt;

A deliberately small Laravel 13 application with clear layers, a Filament admin for
content, and a delivery pipeline that refuses to ship anything below 100% test coverage.

## 1. System context

```mermaid
flowchart LR
    visitor([Visitor]) -->|HTTPS| site[Public site<br/>Blade + Tailwind]
    owner([Owner]) -->|HTTPS| admin[Admin panel<br/>Filament /admin]
    site --> app[(Laravel 13 app<br/>Octane · FrankenPHP)]
    admin --> app
    app --> db[(PostgreSQL / SQLite)]
    app --> cache[(Redis<br/>cache · sessions · queue)]
    app -->|queued mail| mail[SMTP / Mailpit]
    mail --> owner
```

| Concern | Choice | Why |
|---|---|---|
| Runtime | PHP 8.5, FrankenPHP + Laravel Octane | One binary, worker mode, HTTP/2/3, tiny image |
| Public UI | Blade components + Tailwind 4 | Fast, zero JS framework needed for a portfolio |
| Admin | Filament 5 (Livewire 4) | Full CRUD for content in minutes, fully testable |
| Data | PostgreSQL (prod/dev), SQLite in-memory (tests) | Parity in CI via a pgsql matrix job |
| Mail | Queued notification | Contact form stays fast; retries on SMTP failure |

## 2. Layers

```mermaid
flowchart TB
    subgraph HTTP["HTTP layer — App\Http"]
        MW[Middleware<br/>SecurityHeaders · throttle:contact]
        C[Controllers<br/>final, thin]
        R[Form Requests<br/>validation + honeypot]
    end
    subgraph ADMIN["Admin layer — App\Filament"]
        RES[Resources] --> SCH[Schemas / Tables]
        W[Widgets]
    end
    subgraph DOMAIN["Domain"]
        A[Actions<br/>final readonly · handle]
        M[Models<br/>scopes · casts · helpers]
        E[Enums]
        N[Notifications]
    end
    DB[(Database)]

    MW --> C
    C --> R
    C --> A
    C --> M
    A --> M
    A --> N
    RES --> M
    W --> M
    M --> E
    M --> DB
```

**Rules (enforced by `tests/Arch/ArchTest.php`, so CI fails if they are broken):**

- Every file uses `declare(strict_types=1)`.
- Controllers are `final`, suffixed `Controller`, never call `DB::` directly and never depend on each other.
- Actions are `final readonly` with one public `handle()` method and know nothing about HTTP.
- Models only extend Eloquent; they never import `App\Http`, `App\Filament` or `App\Actions`.
- The domain (`Models`, `Actions`, `Http`, `Notifications`, `Enums`) never depends on the admin layer.
- `env()` is only read inside `config/`. No `dd`, `dump`, `var_dump` or `ray` anywhere.
- Pest's `php`, `security` and `laravel` presets apply on top.

## 3. Domain model

```mermaid
erDiagram
    USERS {
        bigint id PK
        string email UK
        bool is_admin "not mass assignable"
    }
    PROFILES {
        bigint id PK
        string name
        string headline
        text bio
        json social_links
        bool available_for_work
    }
    SKILLS {
        bigint id PK
        string name
        string category "SkillCategory enum"
        int sort_order
    }
    EXPERIENCES {
        bigint id PK
        string company
        string role
        date started_at
        date ended_at "null = current"
    }
    PROJECTS {
        bigint id PK
        string slug UK
        string title
        longtext body "Markdown, HTML stripped"
        json tech_stack
        bool is_featured
        timestamp published_at "null = draft, future = scheduled"
    }
    CONTACT_MESSAGES {
        bigint id PK
        string email
        text message
        ip ip_address
        timestamp read_at "null = unread"
    }
```

`Profile` is a singleton: `Profile::current()` returns the saved row, or an unsaved placeholder
so the site renders before setup. The admin resource allows exactly one to be created and none deleted.

## 4. Request flows

### Contact form

```mermaid
sequenceDiagram
    autonumber
    actor V as Visitor
    participant RL as throttle:contact (3/min/IP)
    participant CC as ContactController
    participant FR as StoreContactMessageRequest
    participant A as SubmitContactMessage
    participant DB as Database
    participant Q as Queue
    actor O as Owner

    V->>RL: POST /contact
    RL->>CC: allowed
    CC->>FR: validate (+ honeypot "website" must be missing)
    FR-->>V: 302 back with errors (if invalid)
    CC->>A: handle(payload, ip)
    A->>DB: insert contact_messages
    A->>Q: NewContactMessage (ShouldQueue)
    CC-->>V: 302 /#contact + flash "Thanks"
    Q-->>O: email (reply-to = visitor) with link to /admin
```

### Project page

`GET /projects/{slug}`: route-model binding by `slug` → `ProjectController::show` → `abort_unless($project->isPublished(), 404)`,
so drafts and scheduled projects are never public, even if someone knows the slug.

## 5. Security

- Admin access: `User::canAccessPanel()` requires `is_admin`, which is not in `#[Fillable]`.
- Markdown is rendered with `html_input: strip` and `allow_unsafe_links: false`.
- `SecurityHeaders` middleware adds `X-Content-Type-Options`, `X-Frame-Options`, `Referrer-Policy`,
  `Permissions-Policy`, plus HSTS over HTTPS. `URL::forceHttps()` in production.
- `Model::shouldBeStrict()` outside production (catches lazy loading and silently discarded attributes).
- `DB::prohibitDestructiveCommands()` in production.
- Contact form: rate limit, honeypot, length limits.

## 6. Delivery pipeline

```mermaid
flowchart LR
    dev[git push / PR] --> ci

    subgraph ci["CI — .github/workflows/ci.yml"]
        q[quality<br/>Pint · Rector · Larastan max · composer audit · vet]
        t1[tests PHP 8.5 sqlite<br/>Pest --parallel --coverage --min=100]
        t2[tests PHP 8.5 pgsql]
        t3[tests PHP 8.6<br/>experimental]
        fe[assets<br/>npm ci · build · audit]
        q & t1 & t2 & fe --> img[docker build prod<br/>push ghcr.io on main]
    end

    img -->|main, green| dep["Deploy — deploy.yml<br/>POST Laravel Cloud deploy hook"]
    dep --> cloud[(Laravel Cloud)]
    img -.->|any container host| other[(VPS / Fly / ECS / k8s)]
```

## 7. Runtime images (`docker/Dockerfile`)

| Stage | Based on | Used by |
|---|---|---|
| `base` | `dunglas/frankenphp:1-php8.5` + extensions | all |
| `dev` | base + Composer, Node 22, PCOV | `compose.yaml`, local tooling |
| `vendor` | `composer:2` | build: `--no-dev`, optimized autoloader |
| `assets` | `node:22-alpine` | build: `npm ci && npm run build` |
| `prod` | base + app + vendor + `public/build` | GHCR image, any container host |

`docker/entrypoint.sh` runs `optimize`, `filament:optimize`, and (when `RUN_MIGRATIONS=true`) `migrate --force`,
then hands over to Octane.

## 8. Directory map

```
app/
├── Actions/            single-purpose domain operations
├── Enums/              backed enums (also Filament labels)
├── Filament/           admin panel: Resources/{X}/{Pages,Schemas,Tables}, Widgets
├── Http/
│   ├── Controllers/    final, thin
│   ├── Middleware/     SecurityHeaders
│   └── Requests/       validation
├── Models/
├── Notifications/
└── Providers/          App + Filament panel
config/portfolio.php    admin + contact settings
docker/                 Dockerfile, entrypoint, php.ini overrides
resources/views/        Blade pages + components
tests/{Arch,Unit,Feature}
```
