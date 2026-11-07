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
   Or use the [Cloud CLI](https://github.com/laravel/cloud-cli), installed **globally**. It is a Laravel app that
   registers the `App\` namespace, so it must not be a project dependency:
   ```bash
   composer global require laravel/cloud-cli
   cloud auth -n
   cloud ship -h      # review the options, then run `cloud ship -n …`
   ```
3. **Environment → Settings → General**: PHP **8.5** (switch to 8.6 when Cloud offers it).
4. **Resources**: attach
   - a **PostgreSQL** database (connection vars are injected automatically),
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

### Useful CLI commands

```bash
cloud deploy <app> production -n --open
cloud deploy:monitor -n
cloud tinker production --code='echo App\Models\User::count();' -n
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

## Rollback

- **Laravel Cloud**: *Deployments* → pick a previous deployment → *Redeploy*.
- **Docker**: run the previous `:sha-<commit>` tag.
- Migrations are forward-only. Write reversible `down()` methods and avoid destructive changes in a single release.
