# Tito Admin UI (Laravel + Livewire)

Admin portal for Tito: staff login, staff CRUD, and journals (list / accept / reject / create). Consumes the **j-payments (Spring Boot) GraphQL API**; no backend code changes required.

**Stack:** Laravel 11, Livewire 3, Tailwind CSS, Guzzle (GraphQL client).

---

## Can this be hosted on Vercel?

**Not recommended.** Laravel + Livewire need a **PHP runtime and persistent process** (sessions, server-side Livewire). Vercel is built for serverless/static and has no official PHP runtime; community PHP runtimes have limits (e.g. 60s timeout, read-only filesystem, no long-lived sessions). Livewire’s server-side rendering and session state don’t fit that model well.

**Recommended:** Host on **Render** (same as tito-api), a VPS (Laravel Forge, Ploi), or any host with PHP 8.2+ (e.g. Docker). This repo includes a **Render**-ready setup (see [Deployment](#deployment)).

---

## Requirements

- PHP 8.2+
- Composer
- Node.js & npm (for Tailwind)
- Backend: j-payments api-service (e.g. `https://tito-api.onrender.com`) with Keycloak realm `backoffice-local` and staff users

---

## Quick start (local)

```bash
# From repo root
composer install
cp .env.example .env
php artisan key:generate

# Set in .env:
# TITO_API_URL=https://tito-api.onrender.com
# TITO_GRAPHQL_ENDPOINT="${TITO_API_URL}/graphql"

# Sessions: use file (default) or database/redis for production
# For dev with IP whitelist off: ICE_CASH_IP_WHITELIST_DISABLE_CHECK=true on api-service

npm install && npm run build
php artisan serve
```

Open http://localhost:8000 → Login with a backoffice staff user (e.g. `gregm` / `123456` if seeded).

---

## Environment variables

| Variable | Description |
|----------|-------------|
| `TITO_API_URL` | api-service base URL (e.g. `https://tito-api.onrender.com`) |
| `TITO_GRAPHQL_ENDPOINT` | Optional; defaults to `{TITO_API_URL}/graphql` |
| `APP_KEY` | Laravel app key (`php artisan key:generate`) |

Session driver: `file` (default), or `database` / `redis` for production.

---

## Deployment (Render)

1. **New Web Service** on [Render](https://dashboard.render.com): connect this repo.
2. **Runtime:** PHP (Render supports PHP; or use Docker with a PHP 8.2 image).
3. **Build:** `composer install --no-dev --optimize-autoloader && npm ci && npm run build`
4. **Start:** `php artisan serve --host=0.0.0.0 --port=$PORT`
5. **Env:** Set `TITO_API_URL` (e.g. `https://tito-api.onrender.com`), `APP_KEY` (run `php artisan key:generate --show` and set it). Ensure api-service allows the Render service IP in the IP whitelist (or set `ICE_CASH_IP_WHITELIST_DISABLE_CHECK=true` for dev).

A **render.yaml** is included for optional Blueprint deploy. You can also add the service manually and set the build/start commands and env vars as above.

---

## Build plan reference

Full specification: **j-payments** repo → `docs/ADMIN_UI_LARAVEL_LIVEWIRE_BUILD_PLAN.md`.

Phases: **A** (auth, token, refresh, logout), **B** (staff list/create/edit), **C** (journals list/accept/reject/create), **D** (role-based menu), **E** (UI polish, deploy, docs).
