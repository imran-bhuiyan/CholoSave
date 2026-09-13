# CholoSave Production Deployment Guide

This documents how to deploy the **existing** application: a Laravel 12 (PHP
8.2+) web app plus a Python/Flask AI tips microservice, backed by MySQL.

## Components

| Component | Directory | Runtime |
| --- | --- | --- |
| Web app (UI + API) | repo root | PHP-FPM + Nginx/Apache, docroot `public/` |
| AI tips service | `ai_backend/` | Gunicorn (Flask) |
| Database | — | MySQL 8 |

No queue worker and no scheduler cron are required: the app runs
`QUEUE_CONNECTION=database` but dispatches no jobs, and defines no scheduled
tasks.

## 1. Laravel `.env` (production values)

Copy `.env.example` to `.env` on the server and set:

```env
APP_NAME=CholoSave
APP_ENV=production
APP_KEY=            # generate on the server: php artisan key:generate
APP_DEBUG=false
APP_URL=https://your-domain.example

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=cholosave
DB_USERNAME=cholosave          # dedicated least-privilege MySQL user
DB_PASSWORD=<strong password>

SESSION_DRIVER=database
SESSION_SECURE_COOKIE=true     # requires HTTPS

# REQUIRED for OTP emails (payment + password reset). With MAIL_MAILER=log
# OTPs are only written to storage/logs/laravel.log and users get nothing.
MAIL_MAILER=smtp
MAIL_HOST=<smtp host>
MAIL_PORT=587                  # use 465 with MAIL_SCHEME=smtps
MAIL_USERNAME=<smtp user>
MAIL_PASSWORD=<smtp password>
MAIL_FROM_ADDRESS="noreply@your-domain.example"
MAIL_FROM_NAME="${APP_NAME}"

# Public HTTPS base URL of the AI service (see section 2).
AI_SERVICE_URL=https://ai.your-domain.example
```

Everything marked `[PRODUCTION]` in `.env.example` carries the same notes.

## 2. AI tips service (`ai_backend/`)

```bash
cd ai_backend
python3 -m venv venv && . venv/bin/activate
pip install -r requirements.txt
```

Environment variables (set them on the service, or in `ai_backend/.env` —
real environment variables take precedence):

| Variable | Required | Notes |
| --- | --- | --- |
| `GROQ_API_KEY` | yes | Groq API key; the LLaMA calls fail without it |
| `ALLOWED_ORIGINS` | yes | Comma-separated CORS origins, e.g. `https://your-domain.example`. Defaults to the localhost dev origins |
| `FLASK_DEBUG` | no | `1` enables the Werkzeug debugger — **local development only, never production** |

Run in production (never `python generate_tips.py`, which is the dev entry):

```bash
gunicorn --workers 2 --bind 127.0.0.1:5000 generate_tips:app
```

Put a TLS reverse proxy in front of it (same Nginx as the app or a separate
`ai.` subdomain) and point `AI_SERVICE_URL` at it. The browser calls this
service directly from the AI tips page, so it must be reachable over HTTPS
from the frontend origin.

## 3. Build and deploy steps (web app)

```bash
composer install --no-dev --optimize-autoloader
npm ci
npm run build
php artisan key:generate       # once, if .env is fresh
php artisan migrate
php artisan config:cache && php artisan route:cache && php artisan view:cache
```

`storage/` and `bootstrap/cache/` must be writable by the web server user.
Do not run `php artisan db:seed` in production (it creates a test user).

## 4. Secrets

- Never commit `.env` or `ai_backend/.env` (both are gitignored — verify with
  `git status` before pushing).
- `APP_KEY`, DB passwords, SMTP credentials, and `GROQ_API_KEY` are generated
  on the server / stored in your secrets manager, not in the repository.
