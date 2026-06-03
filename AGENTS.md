# GudangKu — AGENTS.md

## Stack

**Laravel 12** + **React 19** + **Inertia v2** (SPA, SSR) + **Tailwind v4** + **shadcn/ui** (new-york, non-RSC) + **Pest 4**

## Commands

| Command | Purpose |
|---------|---------|
| `composer run dev` | Starts dev server + queue + Vite concurrently |
| `composer run setup` | Full fresh setup (composer install, .env, key, migrate, npm install, build) |
| `composer run test` | Config clear + lint check + Pest |
| `npm run types` | TypeScript check (`tsc --noEmit`) |
| `npm run lint` | ESLint (flat config, `eslint.config.js`) |
| `npm run format` | Prettier (semi, singleQuote, tabWidth 4) |
| `php artisan test --compact --filter=testName` | Run single Pest test |
| `vendor/bin/pint --dirty` | Auto-fix PHP code style (must run before finalizing) |

Full CI check order: `vendor/bin/pint --dirty` → `npm run format:check` → `npm run lint` → `npm run types` → `php artisan test`.

## Testing (Pest 4)

- SQLite `:memory:`, `RefreshDatabase` trait applied globally in `tests/Pest.php`.
- CSRF bypassed globally in tests (`withoutMiddleware(ValidateCsrfToken::class)`).
- Custom helpers in `tests/Pest.php`: `createUserWithRole()`, `createSuperAdmin()`, `createAdmin()`, `createViewer()`, `createRoles()`.
- Assert Inertia pages with `$response->assertInertia()`.
- Create tests: `php artisan make:test --pest {Name}`.

## Architecture

- **Laravel 12 streamlined**: No `app/Http/Kernel.php` or `app/Console/Kernel.php`. Middleware in `bootstrap/app.php`, providers in `bootstrap/providers.php`.
- **Domain actions** in `app/Actions/` (single-responsibility classes per domain).
- **15 Laravel controllers** + **26 shared React components** (including shadcn/ui in `components/ui`).
- **18 Inertia page directories** under `resources/js/pages/`.
- **3 layouts**: app, auth, settings.
- **Generated code** in `resources/js/{actions,routes,wayfinder}/` (Laravel Wayfinder) — gitignored, regenerated on `npm run build`.
- **Soft deletes**: `User`, `Warehouse` models.
- **Rate limiting**: custom named limiters `bulk` (bulk deletes) and `crud` (CRUD operations).

## Roles & Permissions

Spatie Permission v6 with 3 roles: `super-admin`, `admin`, `viewer`. 14 policy classes in `app/Policies/`.

## Notifications

Swappable via `.env`: `NOTIFICATION_CHANNEL=email` (default, `EmailNotificationService`) or `whatsapp` (`FonteService`/Fonnte).

## Business Content

All documentation (README, presentation, flash messages) is in **Indonesian**. Timezone: `Asia/Jakarta`.

## Key Config Files

- `.github/copilot-instructions.md` — detailed Laravel Boost conventions (read this first for code style rules)
- `vite.config.ts` — Wayfinder plugin, React Compiler babel plugin, Tailwind v4, SSR enabled
- `components.json` — shadcn/ui config (`@/components/ui`)
- Tailwind v4: no `tailwind.config.js` — CSS-based config with `@theme` in `resources/css/app.css`
- `boost.json` — Laravel Boost MCP skills configuration

## DB & Migrations

**SQLite** in development (`database/database.sqlite` committed). 19 migrations, 9 seeders (only `UserSeeder` active by default), 13 factories.
