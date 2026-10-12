# Session notes — $0 deploy (Oracle blocked → Render + Neon)

Last updated: 2026-10-11. Status: code ready, NOT yet deployed to Render.

## Why
Oracle account signup blocked (country). US clouds (AWS/GCP/Oracle) same story.
Decision: strictly $0 stack — Render Free web + Neon Free Postgres. No worker,
no Redis, no Reverb on free tier.

## $0 architecture (agreed)
- App: Render Free Web Service (Docker, sleeps after 15min idle, ephemeral disk)
- DB: Neon Free Postgres, project `Thunder` (id `damp-tooth-07301022`),
  branch `production` (`br-gentle-bonus-b5qxwyea`), org `org-plain-bird-21625922`
- Queue: `sync` (no worker needed — app notifies sync + best-effort broadcast)
- Broadcast: `log` on Render (Reverb needs always-on; local dev keeps reverb)
- Cache/Session: `database` (no Redis)
- Storage: local disk for now (EPHEMERAL on Render — receipts vanish on sleep).
  Neon `receipts` bucket (private) provisioned, code switch PENDING.
- Mail: `log` for now, Brevo Free later.

## Changes made today (uncommitted — `git status` shows them)
- `Dockerfile`: + `libpq-dev`, `pdo_pgsql` (kept `pdo_mysql` for XAMPP)
- `render.yaml` (new): free web service, healthcheck `/up`, env defaults
  (`pgsql`/`sync`/`log`/`database`), `DB_URL`/`APP_KEY`/`APP_URL` as sync:false
- `neon.ts` (new): `buckets: { receipts: { access: "private" } }`
  (top-level syntax — fixed preview→buckets deprecation warning)
- `config/filesystems.php`: s3 disk accepts `AWS_ENDPOINT_URL_S3`/`AWS_REGION`
  (what `neon deploy` pulls) as fallback
- `.env.example`: Neon `DB_URL` docs, `QUEUE_CONNECTION=sync`,
  broadcast/storage caveats, Neon storage vars
- `.gitignore`: + `.env.local`, `.neon`
- `package.json` + `package-lock.json`: + `@neon/config` (neon.ts dep)
- Tests: 39 passed (`php artisan test`)

## Neon state
- Linked dir: `/c/xampp/htdocs/thunder` (first attempt wrongly linked `~/Desktop`
  — cleaned: removed stray `~/Desktop/.neon` + `~/Desktop/.env.local`)
- `neon deploy` OK: "No changes — branch already matches", 7 vars pulled
  into local `.env`: `DATABASE_URL`, `DATABASE_URL_UNPOOLED`, `NEON_BRANCH`,
  `AWS_ACCESS_KEY_ID`, `AWS_SECRET_ACCESS_KEY`, `AWS_ENDPOINT_URL_S3`, `AWS_REGION`
- Local `.env` still `DB_CONNECTION=sqlite` (dev unaffected). Note: it has
  `BROADCAST_CONNECTION=reverb` 3x (last wins, local-only, leave it) and a
  trycloudflare `APP_URL` (local-only).

## TODO next session
1. Render dashboard → service env: `DB_URL` (= local `.env` `DATABASE_URL`),
   `APP_KEY` (`php artisan key:generate --show`), `APP_URL` (= Render URL).
   (Secrets stay in dashboard — never paste in chat, never commit.)
2. Deploy, then Render Shell once:
   `php artisan migrate --force && php artisan storage:link`
3. Verify: `/up`, admin login, top-up approve → wallet credit.
4. THEN: switch receipt upload + admin preview to Neon `receipts` bucket
   (private → needs signed URLs for Filament ImageColumn + receipt-preview).
   Files: `TopUpRequestController@store`, `TopUpRequestResource::receiptUrl`,
   game image uploads.
