<?php

use App\Http\Controllers\ProfileController;
use App\Http\Middleware\TenantContext;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        $user = auth()->user();
        
        // Try to get primary tenant from pivot table
        $primaryTenant = $user->tenants()->wherePivot('is_primary', true)->first();
        
        // Fallback to user's tenant_id
        if (!$primaryTenant && $user->tenant) {
            $primaryTenant = $user->tenant;
        }
        
        if ($primaryTenant) {
            return redirect()->route('tenant.dashboard', ['tenant' => $primaryTenant->slug]);
        }
        
        // No tenant found - show dashboard without tenant context
        return redirect()->route('dashboard');
    }
    
    return redirect()->route('login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| Tenant Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', TenantContext::class])
    ->prefix('t/{tenant}')
    ->name('tenant.')
    ->group(function () {
        // Dashboard
        Route::get('/dashboard', function () {
            return view('tenant.dashboard');
        })->name('dashboard');

        // Tickets
        Route::get('/tickets', function () {
            return view('tenant.tickets.index');
        })->name('tickets.index');

        Route::get('/tickets/create', function () {
            return view('tenant.tickets.create');
        })->name('tickets.create');

        Route::get('/tickets/{ticket}', function ($tenant, $ticket) {
            return view('tenant.tickets.show', ['ticketId' => $ticket]);
        })->name('tickets.show');

        // Admin
        Route::prefix('admin')->name('admin.')->middleware('can:admin.users.manage')->group(function () {
            Route::get('/users', function () {
                return view('tenant.admin.users');
            })->name('users.index');
        });

        // Audit
        Route::get('/audit', function () {
            return view('tenant.audit.index');
        })->middleware('can:audit.read')->name('audit.index');
    });

require __DIR__.'/auth.php';
