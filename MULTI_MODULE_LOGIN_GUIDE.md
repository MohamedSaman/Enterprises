# Multi-Module Login System - Implementation Guide

## Overview

This document describes the multi-module login system that allows users to access either the "Invontery" or "Production" modules with role-based access (Admin or Staff).

## Changes Made

### 1. Database Changes

**File:** `database/migrations/2026_04_01_000001_add_module_to_users_table.php`

Added a new column to the users table:

```php
$table->enum('module', ['invontery', 'production', 'both'])->default('invontery');
```

This allows each user to:

- Access only "invontery" module
- Access only "production" module
- Access both modules

### 2. User Model Updates

**File:** `app/Models/User.php`

- Added 'module' to the `$fillable` array
- Added new method `hasModuleAccess($module)` to check if user has permission to access a specific module

```php
public function hasModuleAccess($module)
{
    if ($this->module === 'both') {
        return true;
    }
    return $this->module === $module;
}
```

### 3. Login Component Updates

**File:** `app/Livewire/CustomLogin.php`

- Added `$selectedModule = 'invontery'` property to track the current module selection
- Added `setModule($module)` method to allow users to switch between module tabs
- Updated `login()` method to:
  - Validate that the user has access to the selected module
  - Store the active module in the session
  - Redirect to the appropriate dashboard based on module AND role

**Redirect Logic:**

```
Production Module:
├── Admin → production.admin.dashboard
└── Staff → production.staff.dashboard

Invontery Module:
├── Admin → admin.dashboard
└── Staff → staff.dashboard
```

### 4. Login View Updates

**File:** `resources/views/livewire/custom-login.blade.php`

- Changed module tabs from links to buttons
- Added Livewire click handlers: `wire:click="setModule('moduleName')"`
- Dynamic active class based on `$selectedModule`
- Added CSS for button styling

**Before:**

```blade
<a href="{{ route('welcome') }}" class="module-tab active">Invontery</a>
<a href="javascript:void(0)" class="module-tab">Production</a>
```

**After:**

```blade
<button type="button" wire:click="setModule('invontery')"
    class="module-tab {{ $selectedModule === 'invontery' ? 'active' : '' }}">
    Invontery
</button>
<button type="button" wire:click="setModule('production')"
    class="module-tab {{ $selectedModule === 'production' ? 'active' : '' }}">
    Production
</button>
```

### 5. Production Module Components

**Files:**

- `app/Livewire/Production/Admin/ProductionAdminDashboard.php`
- `app/Livewire/Production/Staff/ProductionStaffDashboard.php`
- `resources/views/livewire/production/admin/production-admin-dashboard.blade.php`
- `resources/views/livewire/production/staff/production-staff-dashboard.blade.php`

Created placeholder dashboards for the production module. Update these with your actual production features.

### 6. Routes Updated

**File:** `routes/web.php`

Added new route groups for production module:

```php
// Production Admin Routes
Route::middleware('role:admin')->prefix('production/admin')->name('production.admin.')->group(function () {
    Route::get('/dashboard', ProductionAdminDashboard::class)->name('dashboard');
});

// Production Staff Routes
Route::middleware('role:staff')->prefix('production/staff')->name('production.staff.')->group(function () {
    Route::get('/dashboard', ProductionStaffDashboard::class)->name('dashboard');
});
```

## Login Flow Diagram

```
User Login Page (2 Tabs: Invontery | Production)
     ↓
User Clicks Tab → setModule() updates $selectedModule
     ↓
User Enters Email & Password
     ↓
User Clicks Login
     ↓
login() method:
  ├─ Validate email/password
  ├─ Check user has access to $selectedModule
  ├─ Check 2FA if enabled
  ├─ Store active_module in session
  ├─ Authenticate user
  └─ Redirect based on module & role
     ├─ Production + Admin → /production/admin/dashboard
     ├─ Production + Staff → /production/staff/dashboard
     ├─ Invontery + Admin → /admin/dashboard
     └─ Invontery + Staff → /staff/dashboard
```

## Next Steps

### 1. Run Database Migration

```bash
php artisan migrate
```

### 2. Update Existing Users

Set the `module` field for existing users:

```bash
php artisan tinker
# Then in Tinker:
> \App\Models\User::all()->each->update(['module' => 'both'])
```

Or via SQL:

```sql
UPDATE users SET module = 'both' WHERE 1=1;
```

### 3. Customize Module Assignment

Decide your user module assignment strategy:

- All staff can access both modules: `module = 'both'`
- Different staff for different modules: assign individually
- Default new users to one module: update default in migration

### 4. Enhance Production Dashboards

Update the production dashboard files with your actual production features:

- `resources/views/livewire/production/admin/production-admin-dashboard.blade.php`
- `resources/views/livewire/production/staff/production-staff-dashboard.blade.php`

### 5. Add Production Module Routes

Add routes for production features in `routes/web.php` within the production route groups:

```php
Route::middleware('role:admin')->prefix('production/admin')->name('production.admin.')->group(function () {
    Route::get('/dashboard', ProductionAdminDashboard::class)->name('dashboard');
    // Add more production admin routes here
    Route::get('/orders', ProductionOrders::class)->name('orders');
    Route::get('/workers', ProductionWorkers::class)->name('workers');
    // etc...
});
```

## Error Handling

When a user without access to a module tries to login:

```
Login Error Message: "This user doesn't have access to the production module"
```

This validation happens in the CustomLogin component's `login()` method:

```php
if (!$user->hasModuleAccess($this->selectedModule)) {
    $this->addError('email', "This user doesn't have access to the {$this->selectedModule} module.");
    $this->password = '';
    return;
}
```

## Session Management

The active module is stored in the session:

```php
session()->put('active_module', $this->selectedModule);
```

You can access it anywhere in your application:

```php
$activeModule = session()->get('active_module'); // 'invontery' or 'production'
```

## Customization Tips

### Change Default Module

In `CustomLogin.php`, change:

```php
public $selectedModule = 'production'; // Change from 'invontery'
```

### Add Module Validation Middleware

Create a middleware to ensure users stay within their assigned module:

```bash
php artisan make:middleware CheckModuleAccess
```

Then in your routes:

```php
Route::middleware(['auth', 'check.module.access'])->group(function () {
    // Protected routes
});
```

### Update Redirect After Login

If you want to customize redirects, modify the `login()` method in `CustomLogin.php`.

## Testing

1. Create test users with different module assignments:
   - User A: module = 'invontery'
   - User B: module = 'production'
   - User C: module = 'both'

2. Test each scenario:
   - User A tries to login to invontery → Success ✓
   - User A tries to login to production → Error ✗
   - User C tries both → Both work ✓

## Troubleshooting

**Issue:** "This user doesn't have access to the module" error

**Solution:** Check the user's module field in database:

```bash
php artisan tinker
> \App\Models\User::find(1)->module
```

**Issue:** After login, user sees wrong dashboard

**Solution:** Check active_module session and redirects in `CustomLogin.php` login() method

**Issue:** Production routes return 404

**Solution:**

1. Ensure migration ran: `php artisan migrate --help`
2. Check routes: `php artisan route:list | grep production`
3. Verify components exist in `/app/Livewire/Production/`
