# Thunder — Albarq Store (Laravel 12)

Arabic RTL storefront + Filament admin (`/admin-panelx2`), wallet + bank-transfer top-ups,
DB-driven games catalog with USD→SDG exchange rates, Google OAuth.

## Local dev (XAMPP, current workflow — unchanged)

```bash
composer run setup   # install, .env, key, migrate, storage:link, npm build
composer run dev     # serve + queue:listen + pail + vite + reverb
composer run test    # config:clear + artisan test
```

## Production deploy (minimal checklist)

1. Env: copy `.env.example` → `.env` and set:
   `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL=https://your-domain`,
   MySQL (`DB_CONNECTION=mysql` + host/db/user/pass), `MAIL_MAILER=smtp` + from address,
   `QUEUE_CONNECTION`/`CACHE_STORE`/`SESSION_DRIVER` (redis recommended),
   `EXCHANGE_RATE_SDG_PER_USD`, Google OAuth trio (or leave empty to disable),
   Reverb keys + `REVERB_ALLOWED_ORIGINS=https://your-domain` + rate limiting on.
2. Build: `composer install --no-dev -o && npm ci && npm run build`
3. Migrate: `php artisan migrate --force && php artisan storage:link`
4. Optimize: `php artisan config:cache && php artisan route:cache && php artisan view:cache`
5. Processes (supervised): `php artisan queue:work`, `php artisan schedule:run` (cron),
   `php artisan reverb:start` only if realtime is enabled (TLS in front).
6. Verify: `php artisan test`, check `/up`, admin login, top-up approve → wallet credit,
   backups + log monitoring (`LOG_LEVEL=info` or `error`) configured.

Docker alternative: `docker build -t thunder .` (Apache serves `public/`, runs `storage:link`).
CI (`.github/workflows/ci.yml`) runs `npm run build` + `php artisan test` on push/PR.

## Money safety

Balances/transactions are `decimal(14,2)`; `WalletService::credit/debit(string|float|int)`
normalizes to 2-decimal strings with `bcadd/bcsub/bccomp`. Never cast amounts to `float`
except for display via `number_format`.
