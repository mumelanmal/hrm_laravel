## Quick orientation

This repository is a Laravel (v12+) starter kit built around Livewire (Flux + Volt) and Laravel Fortify for auth. Key runtime pieces:

- Frontend: Vite + Tailwind via `package.json` + Livewire Flux components (use `<flux:...>` tags in views).
- Page routing & UI pages: Livewire Volt mounts `resources/views/livewire` and `resources/views/pages` (see `app/Providers/VoltServiceProvider.php`).
- Authentication: Laravel Fortify is wired to Livewire views in `app/Providers/FortifyServiceProvider.php` (login/register/reset/etc. map to `livewire.auth.*`).
- Data model: `App\Models\User` uses `TwoFactorAuthenticatable`, `password` is cast as `hashed` and two-factor attributes are hidden.

## Files you should open first

- `app/Providers/FortifyServiceProvider.php` — where Fortify actions, views and rate-limiting are configured.
- `app/Providers/VoltServiceProvider.php` — mounts Volt view paths.
- `routes/web.php` — top-level and Volt routes (settings pages use `Volt::route`).
- `resources/views/livewire/auth/*.blade.php` — auth UI implemented with Flux inputs/buttons (edit these to change auth UX).
- `resources/views/pages/` — Volt-mounted pages (site pages and settings sub-pages).
- `app/Actions/Fortify/` — (create/reset password actions) — business rules for registration and password resets.
- `app/Models/User.php` — model conventions, casts and helper methods (e.g., `initials()`).
- `composer.json` & `package.json` — useful scripts and dependency list.
- `phpunit.xml` — test environment configuration (uses sqlite in-memory by default for CI/local tests).

## Project-specific conventions & patterns

- Livewire+Volt-first: Most interactive UI is implemented as Livewire components and Volt pages. When adding UI, prefer creating a Volt page under `resources/views/pages` or a Livewire component under `resources/views/livewire`.
- Flux components: Views use custom `<flux:input>`, `<flux:button>`, `<flux:link>`, etc. Reuse these when altering forms to maintain consistent styling/behaviour.
- Fortify view mapping: Fortify does not use the default blade locations — it returns views like `view('livewire.auth.login')`. Edit the files under `resources/views/livewire/auth` rather than creating new top-level Fortify blades.
- Rate-limit & throttling: Login/two-factor limits are configured in `FortifyServiceProvider::configureRateLimiting()` — change here for auth throttling.
- Settings routes: Settings pages use `Volt::route('settings/profile', 'settings.profile')` and are grouped under `auth` middleware in `routes/web.php` — prefer Volt routes for account/settings pages.

## Developer workflows (commands)

Primary composer scripts (defined in `composer.json`):

```bash
# initial setup (installs deps, env, migrate, build)
composer run-script setup

# development (runs php artisan serve, queue listener, pail, vite dev in parallel)
composer run-script dev

# run tests
composer run-script test
```

Useful npm scripts (from `package.json`):

```bash
npm run dev    # start vite dev server
npm run build  # build production assets via vite
```

Notes:
- Tests run via Pest/PHPUnit; `phpunit.xml` config uses an in-memory sqlite DB for fast tests.
- The `dev` composer script runs multiple processes with `npx concurrently` (server, queue listener, pail, vite). If you need to reproduce locally, run them individually or use the script.

## Integration points & external dependencies

- Laravel Fortify handles authentication flows (registration, two-factor, password resets). Actions live in `app/Actions/Fortify`.
- Livewire Flux and Volt implement UI patterns — search for `flux:` tags and `Volt::route` to find where UI is declared and routed.
- Vite/Tailwind for frontend tooling; assets built with `npm run build` and served via Vite during dev.

## When making changes, follow these small conventions

- Edit `resources/views/livewire/*` for auth UI. Prefer Flux components for input/buttons.
- Register new pages with `Volt::route(...)` in `routes/web.php` if they belong to the Volt-mounted pages.
- Business logic for auth (create user, password reset) lives under `app/Actions/Fortify` — update there rather than in controllers.
- For tests: use `tests/Feature` and `tests/Unit` (project uses Pest). Tests expect DB_CONNECTION=sqlite and :memory: (see `phpunit.xml`).

## Quick examples (where to change things)

- Change login form fields/styles: edit `resources/views/livewire/auth/login.blade.php` (uses `<flux:input>`).
- Change rate limiting or login throttle: edit `app/Providers/FortifyServiceProvider.php::configureRateLimiting()`.
- Add a new settings page: create `resources/views/pages/settings/<name>.blade.php` and register via `Volt::route('settings/<name>', 'settings.<name>')` inside `routes/web.php` auth group.

If any part of this file is unclear or you'd like more detail (examples for adding a Volt page, creating a Flux input, or merging specific existing guidance), tell me which section to expand and I will update the file.
