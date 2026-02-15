# Deploy Tito Admin to Render

Use these steps to get the Laravel + Livewire admin UI running on Render.

---

## 1. Push code to GitHub

If you haven’t already:

```bash
cd "/Users/gregmorris/Development Projects/tito-admin"
# Create a new repo on GitHub (e.g. gregdigittal/tito-admin), then:
git remote add origin https://github.com/gregdigittal/tito-admin.git
git push -u origin main
```

---

## 2. Create the Web Service on Render

1. Go to [Render Dashboard](https://dashboard.render.com) → **New** → **Web Service**.
2. **Connect repository:** Select **tito-admin** (or connect GitHub and choose the repo).
3. **Name:** `tito-admin` (or e.g. `Tito-admin`).
4. **Region:** e.g. Oregon (same as Tito-api).
5. **Runtime:** **Docker** (the repo has a `Dockerfile`).
6. **Build & Deploy**
   - **Dockerfile path:** `./Dockerfile` (default).
   - No need to set Build Command / Start Command when using Docker; the Dockerfile defines them.
7. **Instance type:** Free or Starter.

---

## 3. Environment variables

In the service → **Environment** tab, add:

| Key | Value |
|-----|--------|
| `TITO_API_URL` | `https://tito-api.onrender.com` (or your api-service URL) |
| `APP_KEY` | Run `php artisan key:generate --show` locally and paste the value |
| `APP_ENV` | `production` |
| `APP_DEBUG` | `false` |
| `APP_URL` | Your Render URL (e.g. `https://tito-admin.onrender.com`) after first deploy |

Optional for production:

- `SESSION_DRIVER` = `file` (default; for sticky sessions you could use Redis later).

---

## 4. Backend (Tito-api) settings

- **IP whitelist:** Add the outbound IP of the Render **tito-admin** service to the api-service IP whitelist, **or** in dev set on Tito-api:  
  `ICE_CASH_IP_WHITELIST_DISABLE_CHECK=true`.
- **CORS:** Not required for server-side Laravel → api-service calls.

**If Render says "duplicate variable" when adding that key on Tito-api:**

1. **Edit instead of add** – The key may already exist. In Tito-api → **Environment**, use the search/filter box and search for `ICE_CASH` or `WHITELIST`. If you find it, edit that row and set the value to `true` instead of adding a new one.
2. **Environment groups** – If Tito-api uses an **Environment Group**, that group may define the same key. Open the group (Dashboard → **Environment Groups** → group attached to Tito-api) and add or edit the variable there, or remove it from the group and set it only on the service.
3. **Case or typos** – Render treats env keys as a single set; a key that differs only by case or a small typo can still conflict. Try searching for a partial name (e.g. `IP_WHITELIST`) to find a similar key.
4. **Sync / refresh** – Reload the Environment tab and scroll the full list; sometimes the duplicate is off-screen.

---

## 5. Deploy

Click **Create Web Service**. Render will build the Docker image and deploy. When it’s live, open the service URL (e.g. `https://tito-admin.onrender.com`) and log in with a backoffice user (e.g. `gregm` / `123456` if seeded).

**If tito-admin doesn’t appear under “Ungrouped Services”** (but recreating says it already exists): it’s likely **inside a project**. On the Overview, click **“My project”** (or the project you used when creating the service). Services that belong to a project are listed there, not under “Ungrouped Services.” The live app is always at **https://tito-admin.onrender.com** even if the dashboard list is confusing.

---

## 6. Optional: Render Blueprint

You can also use **Blueprint** and add a service that points at this repo with runtime Docker and the env vars above; the repo’s `render.yaml` is for reference if you add PHP native later.
