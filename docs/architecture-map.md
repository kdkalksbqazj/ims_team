# Architecture Map

## Goal
This document maps feature ownership by module so code discovery, maintenance, and scaling stay predictable.

## Active Modules

### Analytics & Alerts
- Namespace roots:
  - `App\Modules\Analytics\Controllers`
  - `App\Modules\Analytics\Services`
  - `App\Modules\Analytics\Alerts\Listeners`
- Route entry points:
  - `/dashboard` -> Dashboard controller
  - `/reports` -> Reporting controller
- View paths:
  - `resources/views/modules/analytics/dashboard/index.blade.php`
  - `resources/views/modules/analytics/reports/index.blade.php`
- Service ownership:
  - `DashboardMetricsService`: dashboard counts, low-stock lists, recent activity scope logic
  - `ReportingService`: summary, trends, top-products reporting datasets
- Alert ownership:
  - `SendStockAlert` listener handles low-stock warning logic for `StockUpdated` event

### IAM
- Namespace root:
  - `App\Modules\IAM\Controllers`
- Current route wrapper:
  - Profile controller delegates to legacy HTTP controller implementation

### Organization
- Namespace root:
  - `App\Modules\Organization\Controllers`
- Current route wrapper:
  - Branch controller delegates to legacy HTTP controller implementation

### Catalog
- Namespace root:
  - `App\Modules\Catalog\Controllers`
- Current route wrapper:
  - Product controller delegates to legacy HTTP controller implementation

### Operations
- Namespace root:
  - `App\Modules\Operations\Controllers`
- Current route wrapper:
  - Transaction controller delegates to legacy HTTP controller implementation

## Routing Policy
- Prefer module-namespaced controllers in `routes/web.php` for all feature routes.
- Keep route names stable (e.g., `dashboard`, `transactions.*`) to avoid UI and test breakage.

## View Policy
- Prefer module-specific paths:
  - `resources/views/modules/<module>/<feature>/...`
- Legacy view files can remain as compatibility wrappers that include module views.

## Event & Alert Policy
- Keep alert/listener logic under module ownership.
- Register framework event bindings in providers using module listener classes.

## Suggested Evolution Path
1. Move business logic from legacy HTTP controllers into module services.
2. Keep wrappers temporarily for compatibility.
3. Once stable, deprecate wrappers and remove them in a controlled cleanup release.
