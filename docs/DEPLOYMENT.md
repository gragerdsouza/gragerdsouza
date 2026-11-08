# Deployment

> Author: Grager D'souza &lt;gragerdsouza@gmail.com&gt;

Two supported targets:

1. **Laravel Cloud** (recommended): managed, zero-downtime, deployed by GitHub Actions after CI passes.
2. **Any container host**: the `prod` Docker image is published to GHCR on every green build of `main`.

---

## 1. Laravel Cloud

### One-time setup

1. **Push the repo to GitHub.**
2. **Create the application** in [Laravel Cloud](https://cloud.laravel.com) → *New application* → pick the repo and a region.
   Or use the Cloud CLI (see *Laravel Cloud CLI* below): `make cloud ARGS="ship -h"`.
3. **Environment → Settings → General**: PHP **8.5** (switch to 8.6 when Cloud offers it).
4. **Resources**: attach
   - a **PostgreSQL** database (connection vars are injected automatically). **Required**: Cloud does
     not support SQLite (the filesystem is wiped on every deploy and wake-up), and without a database
     every page returns 500. Don't set `DB_*` variables yourself; they override the injected ones.
   - a **Valkey/Redis** cache (used for cache, sessions and the queue),
   - an **Object Storage bucket**, visibility **public**, disk name **`public`** (profile avatars; the Cloud filesystem is ephemeral).
5. **Build commands**
   ```bash
   composer install --no-dev --optimize-autoloader
   npm ci && npm run build
   php artisan optimize
   php artisan filament:optimize
   ```
6. **Deploy commands**
   ```bash
   php artisan migrate --force
   ```
   Seed once after the first deploy (Cloud → *Commands*, or `cloud command:run <env> --cmd='php artisan db:seed --force' -n`).
   Re-running it is safe: the admin password is only set when `ADMIN_PASSWORD` is set, and the starter content is skipped if a profile exists.
7. **Environment variables** (Cloud → Environment → Variables):

   | Key | Value |
   |---|---|
   | `APP_NAME` | `"Grager D'souza"` |
   | `APP_ENV` / `APP_DEBUG` | `production` / `false` |
   | `ADMIN_EMAIL` / `ADMIN_PASSWORD` | your login (use Cloud **Secrets** for the password) |
   | `CONTACT_NOTIFY_EMAIL` | where contact-form mail goes |
   | `ADMIN_PATH` | optional non-obvious admin URL prefix (default `admin`) |
   | `ADMIN_ALLOWED_IPS` | optional comma-separated IPs/CIDRs allowed into the admin; others get 404 |
   | `ADMIN_REQUIRE_MFA` | leave unset (defaults to `true`): the admin must use an authenticator app |
   | `CACHE_STORE`, `SESSION_DRIVER`, `QUEUE_CONNECTION` | `redis` (or `database`) |
   | `MAIL_*` | your SMTP provider, or attach Resend in Cloud |

8. **Queue worker**: enable a worker (or managed queue) so `NewContactMessage` emails are sent.
9. **Deploy from GitHub Actions, not on push.** Deploys are **off by default**; see *Turning deploys on and off* below.
   - Cloud → Environment → *Deployments*: **disable Push to deploy** and copy the **Deploy hook URL**.
   - GitHub → *Settings → Secrets and variables → Actions* → **New secret**
     `LARAVEL_CLOUD_DEPLOY_HOOK` = the hook URL.
   - Optional: GitHub → *Settings → Environments → production* → add yourself as a required reviewer for manual approval.

### Turning deploys on and off

Automatic deploys are controlled by one GitHub **repository variable**, so you never need a code change:

| | How |
|---|---|
| **Enable** | GitHub repo → *Settings → Secrets and variables → Actions* → **Variables** tab → *New repository variable* → name `LARAVEL_CLOUD_DEPLOY_ENABLED`, value `true` |
| **Pause** | Edit that variable to `false`, or delete it |
| **Check** | *Actions → Deploy*: while paused, the "Laravel Cloud" job shows as **skipped** (grey), not failed |

You still need the `LARAVEL_CLOUD_DEPLOY_HOOK` **secret** (step 9) before enabling, otherwise the job fails with a clear error.
CI (tests, lint, Docker image) runs as normal either way.

### How a release flows

```
push to main ─▶ CI (Pint · Rector · Larastan · Pest 100% · assets · docker)
                     │ all green
                     ▼
               deploy.yml ─▶ POST $LARAVEL_CLOUD_DEPLOY_HOOK ─▶ Cloud builds & switches traffic (zero downtime)
```

- A red CI run means **no deploy**.
- Manual deploy: GitHub → *Actions → Deploy → Run workflow*.
- Your site is at `https://<app>-<env>.laravel.cloud` after the first deploy. Add a custom domain under *Domains*
  (or `cloud domain:create … -n && cloud domain:verify -n`); SSL is automatic.

### Laravel Cloud CLI

The [Cloud CLI](https://github.com/laravel/cloud-cli) needs PHP 8.3+, so it runs in Docker. It is itself a
Laravel app (it registers the `App\` namespace), so it is **not** a project dependency: `make` installs it
into `~/.config/cloud/cli` on your machine, next to the API token it stores. Nothing is added to the repo.

**1. Create an API token** in Laravel Cloud → **Organization settings → API tokens → Create API Token**.
Give it a name (e.g. `cli-macbook`), pick an expiry (6 months is a good balance) and copy it; it is shown once.
Optionally scope it to this application and only the permissions you need.

**2. Save it** (typed input is hidden; the token never lands in shell history or the repo):

```bash
make cloud-login        # installs the CLI on first use, then asks for the token
```

The token is stored in `~/.config/cloud/config.json`. Revoke it in the same Cloud screen if it leaks;
never commit it or paste it into chats or issues. For CI, use a separate, tightly scoped token.

**3. Use it**

```bash
make cloud ARGS="application:list --json -n"
make cloud ARGS="deploy gragerdsouza production -n"
make cloud ARGS="deploy:monitor -n"
make cloud ARGS="command:run production --cmd='php artisan db:seed --force' -n"
make cloud ARGS="tinker production --code='echo App\Models\User::count();' -n"
make cloud-install      # update the CLI
```

---

## 2. Any server (Docker)

Every green build of `main` publishes `ghcr.io/<owner>/<repo>:latest` and `:sha-<commit>`.

```bash
docker run -d --name portfolio -p 80:8000 \
  -e APP_KEY=base64:... -e APP_URL=https://example.com \
  -e DB_CONNECTION=pgsql -e DB_HOST=... -e DB_DATABASE=... -e DB_USERNAME=... -e DB_PASSWORD=... \
  -e REDIS_HOST=... -e CACHE_STORE=redis -e SESSION_DRIVER=redis -e QUEUE_CONNECTION=redis \
  -e MAIL_MAILER=smtp -e MAIL_HOST=... \
  -e ADMIN_EMAIL=you@example.com -e ADMIN_PASSWORD=... \
  -e RUN_MIGRATIONS=true \
  ghcr.io/<owner>/<repo>:latest

# queue worker (same image)
docker run -d --name portfolio-queue --env-file .env.production ghcr.io/<owner>/<repo>:latest php artisan queue:work

# first deploy only
docker exec portfolio php artisan db:seed --force
```

- The image serves HTTP on **:8000** via Octane + FrankenPHP; put it behind your load balancer / reverse proxy for TLS
  (proxies are trusted, so `X-Forwarded-*` headers work).
- Health check: `GET /up` (built into the image's `HEALTHCHECK`).
- `RUN_MIGRATIONS=true` runs `migrate --force` on container start; leave it off on extra replicas.
- Works the same on Fly.io, Render, Railway, ECS, Cloud Run or Kubernetes.

Build locally: `make build-prod` → `portfolio:local`.

---

## Security checklist

Built in (covered by tests):

- **Admin**: password + authenticator-app 2FA with recovery codes (required unless `ADMIN_REQUIRE_MFA=false`),
  Filament login throttling, optional IP allowlist (`ADMIN_ALLOWED_IPS`, 404 for everyone else), optional
  custom path (`ADMIN_PATH`). In production, passwords need 12+ chars with mixed case, numbers and symbols,
  and are rejected if they appear in a known data breach (Have I Been Pwned, k-anonymity).
- **Browser**: strict nonce-based Content-Security-Policy on public pages, HSTS (2 years), `nosniff`,
  frame and opener isolation, and a locked-down Permissions-Policy (`app/Http/Middleware/SecurityHeaders.php`).
- **Input**: rate limits on every public page (`PUBLIC_RATE_LIMIT`) and the contact form, honeypot,
  Markdown rendered with raw HTML stripped, and only `http(s)` links accepted and rendered.
- **Sessions**: encrypted, `HttpOnly`, `SameSite=Lax`, `Secure` in production.

After the first deploy with 2FA, sign in, scan the QR code with your authenticator app and **store the
recovery codes somewhere safe** (password manager). Changing `ADMIN_*` settings needs a redeploy (routes are cached).

Lost your phone and recovery codes? From a Cloud command / `php artisan tinker`:
`App\Models\User::where('email', 'you@example.com')->update(['app_authentication_secret' => null, 'app_authentication_recovery_codes' => null]);`
then sign in and set up 2FA again.

---

## Rollback

- **Laravel Cloud**: *Deployments* → pick a previous deployment → *Redeploy*.
- **Docker**: run the previous `:sha-<commit>` tag.
- Migrations are forward-only. Write reversible `down()` methods and avoid destructive changes in a single release.
