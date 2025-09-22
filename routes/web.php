<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\{TicketController, TicketScanController, EventController, WaitlistController};

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

// Rutas de eventos
Route::prefix('eventos')->name('eventos.')->group(function () {
    Route::get('/', [EventController::class, 'index'])->name('index');
    Route::get('disponibles', [EventController::class, 'disponibles'])->name('disponibles');
    Route::get('crear', [EventController::class, 'create'])->name('create');
    Route::post('/', [EventController::class, 'store'])->name('store');
    Route::get('{evento}', [EventController::class, 'show'])->name('show');
    Route::get('{evento}/editar', [EventController::class, 'edit'])->name('edit');
    Route::put('{evento}', [EventController::class, 'update'])->name('update');
    Route::delete('{evento}', [EventController::class, 'destroy'])->name('destroy');
    Route::get('{evento}/reporte', [EventController::class, 'reporte'])->name('reporte');
});

// Rutas de lista de espera
Route::prefix('lista-espera')->name('waitlist.')->group(function () {
    Route::post('{evento}/agregar', [WaitlistController::class, 'agregar'])->name('agregar');
    Route::delete('{evento}/remover', [WaitlistController::class, 'remover'])->name('remover');
    Route::get('{evento}/mostrar', [WaitlistController::class, 'mostrar'])->name('mostrar');
    Route::post('{evento}/notificar', [WaitlistController::class, 'notificar'])->name('notificar');
});
