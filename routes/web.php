<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\{TicketController, TicketScanController};

Route::middleware('auth')->group(function () {
  Route::post('/tickets', [TicketController::class,'store']);            // issue
  Route::get('/tickets/{ticket}', [TicketController::class,'show']);     // show
  Route::post('/tickets/{ticket}/transfer', [TicketController::class,'transfer']);
  Route::post('/tickets/{ticket}/revoke',   [TicketController::class,'revoke']);

  Route::post('/tickets/redeem', [TicketScanController::class,'redeem']); // by qr_hash
});

Route::get('/', function () {
    return Inertia::render('welcome');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('dashboard');
    })->name('dashboard');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
