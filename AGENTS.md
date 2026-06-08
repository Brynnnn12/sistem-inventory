# GudangKu — AGENTS.md

## Stack

**Laravel 12** + **React 19** + **Inertia v2** (SPA, SSR) + **Tailwind v4** + **shadcn/ui** (new-york, non-RSC) + **Pest 4**

## Commands

| Command | Purpose |
|---------|---------|
| `composer run dev` | Starts dev server + queue + Vite concurrently |
| `composer run dev:ssr` | Dev with SSR + logs + queue |
| `composer run setup` | Full fresh setup (install, .env, key, migrate, npm install, build) |
| `composer run test` | Config clear + lint check + Pest |
| `vendor/bin/pint --dirty` | Auto-fix PHP code style (run before finalizing) |
| `npm run types` | TypeScript check (`tsc --noEmit`) |
| `npm run lint` | ESLint (flat config, `eslint.config.js`) |
| `npm run format` | Prettier (semi, singleQuote, tabWidth 4) |
| `php artisan test --compact --filter=testName` | Run single Pest test |

Full pre-commit order: `vendor/bin/pint --dirty` → `npm run format:check` → `npm run lint` → `npm run types` → `composer run test`.

## Testing (Pest 4)

- SQLite `:memory:`, `RefreshDatabase` applied globally in `tests/Pest.php`.
- CSRF bypassed globally (`withoutMiddleware(ValidateCsrfToken::class)`).
- Helpers in `tests/Pest.php`: `createUserWithRole()`, `createSuperAdmin()`, `createAdmin()`, `createViewer()`, `createRoles()`.
- Assert Inertia pages with `$response->assertInertia()`.
- Create tests: `php artisan make:test --pest {Name}`.

## Architecture

- **Laravel 12 streamlined**: No `app/Http/Kernel.php` or `app/Console/Kernel.php`. Middleware in `bootstrap/app.php`, providers in `bootstrap/providers.php`.
- **Domain actions** in `app/Actions/` (38 classes across 11 domains: Categories, Customers, Employee, Fortify, Opname, Products, Stock, Suppliers, Transaction, Warehouses, WarehouseUsers).
- **19 controllers** in `app/Http/Controllers/` (~15 resource controllers + settings + base).
- **56 React components**: 31 shadcn/ui in `components/ui/` + 25 shared in `components/`.
- **17 Inertia page directories** under `resources/js/pages/`.
- **3 layouts**: app, auth, settings (in `resources/js/layouts/`).
- **Generated code** in `resources/js/{actions,routes,wayfinder}/` (Laravel Wayfinder) — gitignored, regenerated on `npm run build`.
- **Soft deletes** on 5 models: `User`, `Warehouse`, `Product`, `Category`, `WarehouseUser`.

## Rate Limiting

4 custom named limiters in `app/Providers/RateLimitServiceProvider.php`:
- `api`: 60/min, `crud`: 30/min, `bulk`: 10/min (bulk deletes), `critical`: 20/min.
- All return Indonesian error messages on 429.

## Notifications

Swappable via `config('services.notification_channel')` / `NOTIFICATION_CHANNEL` env:
- `whatsapp` (default, `FonteService`/Fonnte)
- `email` (`EmailNotificationService`)
- `.env.example` does **not** include `NOTIFICATION_CHANNEL` or `FONNTE_TOKEN` — add them manually.

## Business Content

All documentation (README, presentation, flash messages) is in **Indonesian**. Timezone: `Asia/Jakarta`.

## Key Config Files

- `.github/copilot-instructions.md` — Laravel Boost conventions (read first for code style rules).
- `vite.config.ts` — Wayfinder plugin, React Compiler babel plugin, Tailwind v4, SSR enabled.
- `components.json` — shadcn/ui config (`@/components/ui`, `new-york`, non-RSC).
- Tailwind v4: no `tailwind.config.js` — CSS-based config in `resources/css/app.css` with `@theme`.
- `boost.json` — Laravel Boost MCP skills configuration.

## DB & Migrations

**SQLite** in development (`database/database.sqlite` committed). **17 migrations**, 9 seeders (only `UserSeeder` active by default), 13 factories.
