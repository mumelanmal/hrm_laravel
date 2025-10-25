## Quick orientation

This is a Laravel (v12+) HRM (Human Resources Management) system built around Livewire (Flux + Volt), Laravel Fortify for auth, and Filament admin panel. Key runtime pieces:

- **Core domain**: Employee management with CSV import/export, resigned employee tracking via **Soft Deletes**, and audit trail via `EmployeeChange` model. Employees have encrypted sensitive fields (`npwp`, `bank_account`, `bpjs_number`).
- **Dual UI paradigm**: 
  - **Filament admin panel** at `/admin` for quick CRUD (`app/Filament/Resources/EmployeeResource.php`, widgets, stats, trash management)
  - **Volt pages** at `/employees/*` for custom workflows (CSV import, filtering by status/aktif, resign flows)
- **Frontend**: Vite + Tailwind via `package.json` + Livewire Flux components (use `<flux:...>` tags in views).
- **Page routing**: Livewire Volt mounts `resources/views/livewire` and `resources/views/pages` (see `app/Providers/VoltServiceProvider.php`). Employee pages use `Volt::route('employees/*', 'employees.*')` in `routes/web.php`.
- **Authentication**: Laravel Fortify wired to Livewire views in `app/Providers/FortifyServiceProvider.php` (login/register/reset/etc. map to `livewire.auth.*`). Users have `is_admin` flag for policy-based access.
- **Data model**: `Employee` (with Soft Deletes for resignations), `EmployeeChange` (audit log). `User` has `TwoFactorAuthenticatable`, `password` cast as `hashed`, `is_admin` boolean.

## Files you should open first

**Core domain & architecture:**
- `app/Models/Employee.php` — main Employee model with encrypted sensitive fields, audit trail via `booted()` hooks that create `EmployeeChange` records on create/update/delete. **Uses Soft Deletes**: resigned employees are soft-deleted (`deleted_at` column) with resignation metadata (`date_resigned`, `alasan_resign`, `keterangan`). Use `Employee::onlyTrashed()` to query resigned employees.
- `app/Models/EmployeeChange.php` — audit log capturing who changed what on employees (stores `user_id`, `action`, `changes` as JSON).
- `app/Policies/EmployeePolicy.php` — authorization: authenticated users can view, only `is_admin` users can create/update/delete/restore/forceDelete.

**Dual UI: Filament + Volt:**
- `app/Filament/Resources/EmployeeResource.php` — Filament admin panel CRUD resource for employees (at `/admin`). Includes TrashedFilter and restore/forceDelete actions.
- `app/Providers/Filament/ManajemenPegawaiPanelProvider.php` — Filament panel config (widgets, auth, path `/admin`).
- `app/Livewire/EmployeeIndex.php` — Livewire component for active employee listing (filtering, sorting, search, pagination, view modes).
- `app/Livewire/ResignedEmployeeIndex.php` — Livewire component for resigned employee listing using `Employee::onlyTrashed()`.
- `resources/views/pages/employees/*.blade.php` — Volt pages for employee workflows (index, create, edit, show, resign).
- `app/Http/Controllers/EmployeeController.php` — handles CSV import/export, resign workflow (soft delete), restore, and force delete.

**Auth & routes:**
- `app/Providers/FortifyServiceProvider.php` — Fortify actions, views and rate-limiting.
- `routes/web.php` — Volt routes for `/employees/*` and `/settings/*`, plus traditional routes for import/export/resign/restore/destroy actions.
- `resources/views/livewire/auth/*.blade.php` — auth UI implemented with Flux inputs/buttons.
- `app/Models/User.php` — user model with `is_admin` flag, two-factor auth, `initials()` helper.
- `database/seeders/AdminUserSeeder.php` — creates default admin user (admin@example.com / password) for dev.

**Dev tooling:**
- `composer.json` & `package.json` — scripts for setup, dev, test.
- `phpunit.xml` — test environment (sqlite in-memory).

## Project-specific conventions & patterns

**Soft Deletes for Resignations:**
- Employee resignations use Laravel's Soft Deletes instead of a separate `resigned_employees` table. When an employee resigns, they are soft-deleted (`deleted_at` is set).
- Resignation metadata stored in `date_resigned`, `alasan_resign`, `keterangan` fields on the Employee model itself.
- Query active employees: `Employee::query()` (default - excludes trashed)
- Query resigned employees: `Employee::onlyTrashed()`
- Query all employees: `Employee::withTrashed()`
- Restore resigned employee: `$employee->restore()` (also clears resignation fields and sets `aktif = 'Aktif'`)
- Permanently delete: `$employee->forceDelete()`

**Dual UI architecture (Filament + Volt):**
- **Filament** at `/admin` for admin CRUD: quick data entry/edits via `EmployeeResource`. Includes trash filter to view/restore soft-deleted employees. Filament panel defined in `ManajemenPegawaiPanelProvider.php`.
- **Volt pages** at `/employees/*` for business workflows: CSV import, filtering by `status_kepegawaian`/`aktif`, resign flows. Prefer Volt pages for custom UI.
- When adding employee features: if it's simple CRUD, add to Filament; if it's a workflow with custom UX, create a Volt page.

**CSV import/export workflow:**
- Import: `EmployeeController@import` reads CSV with BOM stripping, case-insensitive header normalization (`normalize()` function), and flexible column mapping.
- Export: `EmployeeController@export` streams CSV response with all employee fields (encrypted fields are auto-decrypted during export).
- Header normalization allows variant spellings/punctuation in CSV headers (e.g., "Tahun Masuk" = "tahun_masuk" = "TAHUN.MASUK").

**Employee model audit trail:**
- `Employee` model has `booted()` hooks that auto-create `EmployeeChange` records on create/update/delete (including soft deletes).
- Stores `user_id` (who made the change), `action` (created/updated/deleted), `changes` (JSON of affected fields).
- Use this for compliance/audit requirements; no manual logging needed when touching Employee model.

**Data encryption:**
- Sensitive employee fields (`npwp`, `bank_account`, `bpjs_number`) use Laravel's `encrypted` cast — automatically encrypt on write, decrypt on read.
- Be cautious when querying/filtering these fields (can't use WHERE on encrypted data).

**Livewire component patterns:**
- `EmployeeIndex` component manages filter state (`search`, `perPage`, `status`, `aktif`, `sortField`, `sortDirection`, `viewMode`) with `updatesQueryString` for shareable URLs.
- `ResignedEmployeeIndex` uses `Employee::onlyTrashed()` with similar filtering/sorting patterns.
- Filter state resets pagination (`resetPage()`) to avoid empty result pages.
- Sorting uses allowlist (`allowedSorts()`) to prevent SQL injection.

**Authorization:**
- `EmployeePolicy`: authenticated users can view employees; only `is_admin` users can create/update/delete/restore/forceDelete.
- Check `User->is_admin` boolean (set via `AdminUserSeeder` or manually in DB).

**Livewire+Volt-first UI:**
- Most interactive UI uses Livewire components or Volt pages. Prefer `resources/views/pages` for Volt pages, `resources/views/livewire` for Livewire components.
- Flux components: `<flux:input>`, `<flux:button>`, `<flux:heading>`, etc. maintain consistent styling. Always use Flux for forms/UI.

**Fortify view mapping:**
- Fortify returns `view('livewire.auth.login')`, not default blade paths. Edit `resources/views/livewire/auth/*.blade.php` for auth UI changes.
- Rate-limit & throttling configured in `FortifyServiceProvider::configureRateLimiting()`.

**Settings routes:**
- Settings pages use `Volt::route('settings/profile', 'settings.profile')` in `routes/web.php` auth group. Add new settings pages with Volt routes.

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

- **Laravel Fortify**: authentication flows (registration, two-factor, password resets). Actions live in `app/Actions/Fortify`.
- **Livewire Flux and Volt**: UI patterns. Search for `<flux:` tags and `Volt::route` to find where UI is declared and routed.
- **Filament**: admin panel framework at `/admin`. Resources in `app/Filament/Resources`, widgets in `app/Filament/Widgets`.
- **Vite/Tailwind**: frontend tooling; assets built with `npm run build` and served via Vite during dev.

## When making changes, follow these small conventions

- Edit `resources/views/livewire/*` for auth UI. Prefer Flux components for input/buttons.
- Register new pages with `Volt::route(...)` in `routes/web.php` if they belong to the Volt-mounted pages.
- Business logic for auth (create user, password reset) lives under `app/Actions/Fortify` — update there rather than in controllers.
- For tests: use `tests/Feature` and `tests/Unit` (project uses Pest). Tests expect DB_CONNECTION=sqlite and :memory: (see `phpunit.xml`).

## Quick examples (where to change things)

- **Resign an employee**: use `EmployeeController@resign` which soft-deletes with resignation metadata, or via Filament delete action.
- **View resigned employees**: `Employee::onlyTrashed()->get()` or use Filament trash filter, or `ResignedEmployeeIndex` Livewire component.
- **Restore resigned employee**: `EmployeeController@restore` or Filament restore action.
- Change login form fields/styles: edit `resources/views/livewire/auth/login.blade.php` (uses `<flux:input>`).
- Change rate limiting or login throttle: edit `app/Providers/FortifyServiceProvider.php::configureRateLimiting()`.
- Add a new settings page: create `resources/views/pages/settings/<name>.blade.php` and register via `Volt::route('settings/<name>', 'settings.<name>')` inside `routes/web.php` auth group.

If any part of this file is unclear or you'd like more detail (examples for adding a Volt page, creating a Flux input, or merging specific existing guidance), tell me which section to expand and I will update the file.
