<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\EventAdminController;
use App\Http\Controllers\Admin\UserAdminController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
|
| Aquí se definen las rutas del panel de administración.
| Todas las rutas están protegidas por el middleware 'admin'.
|
*/

Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    
    // Dashboard principal
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    
    // CRUD of Events
    Route::resource('events', EventAdminController::class);
    
    // CRUD of Users
    Route::resource('users', UserAdminController::class);
    
    // Additional admin functionalities
    Route::prefix('tickets')->name('tickets.')->group(function () {
        Route::get('/scan', function () {
            return view('admin.tickets.scan');
        })->name('scan');
        
        Route::post('/verify', function (\Illuminate\Http\Request $request) {
            // Implement ticket verification by QR
            $ticketCode = $request->input('ticket_code');
            
            // Log scan
            \Illuminate\Support\Facades\Log::info('Admin scanned ticket', [
                'admin_id' => auth()->id(),
                'admin_email' => auth()->user()->email,
                'ticket_code' => $ticketCode,
                'ip' => $request->ip()
            ]);
            
            // Here you would implement verification logic
            return response()->json([
                'success' => true,
                'message' => 'Ticket verified successfully',
                'ticket_code' => $ticketCode
            ]);
        })->name('verify');
    });
    
    // Reports and statistics
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/events', function () {
            return view('admin.reports.events');
        })->name('events');
        
        Route::get('/users', function () {
            return view('admin.reports.users');
        })->name('users');
        
        Route::get('/sales', function () {
            return view('admin.reports.sales');
        })->name('sales');
    });
});
