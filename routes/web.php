<?php

use Illuminate\Support\Facades\Route;
use App\Enums\EventStatus;
use App\Http\Controllers\{
    DashboardController,
    EventController,
    OrderController,
    PaymentController,
    StripeWebhookController,
    TicketAdminController,
    TicketController,
    WaitlistController,
};
use App\Models\Event;

Route::get('/', function () {
    $events = Event::query()
        ->where('status', EventStatus::published->value)
        ->where('start_time', '>=', now())
        ->orderBy('start_time')
        ->limit(6)
        ->get();

    return view('landing', ['events' => $events]);
})->name('home');

Route::resource('events', EventController::class)->only(['index','create','store','show','edit','update','destroy']);

Route::middleware(['auth'])->group(function () {
    Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
    Route::get('/orders/{order}/checkout', [OrderController::class, 'checkout'])->name('orders.checkout');

    Route::post('/orders/{order}/payments', [PaymentController::class, 'store'])->name('orders.payments.store');
    Route::post('/orders/{order}/pay/stripe', [PaymentController::class, 'stripeCheckout'])->name('orders.payments.stripe.checkout');
    Route::get('/orders/{order}/pay/stripe/success', [PaymentController::class, 'stripeSuccess'])->name('orders.payments.stripe.success');
    Route::get('/orders/{order}/pay/stripe/cancel', [PaymentController::class, 'stripeCancel'])->name('orders.payments.stripe.cancel');

    Route::get('/tickets', [TicketController::class, 'index'])->name('tickets.index');
    Route::get('/tickets/{ticket}/download', [TicketController::class, 'download'])->name('tickets.download');
    Route::get('/tickets/{ticket}/qr', [TicketController::class, 'qr'])->name('tickets.qr');
});

Route::post('/events/{event}/waitlist', [WaitlistController::class, 'store'])
    ->middleware('auth')
    ->name('events.waitlist.store');

Route::post('/stripe/webhook', [StripeWebhookController::class, 'handle'])
    ->name('stripe.webhook')
    ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);

Route::middleware(['auth','can:access-admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/tickets', [TicketAdminController::class, 'index'])->name('tickets.index');
        Route::get('/tickets/scan', [TicketAdminController::class, 'scan'])->name('tickets.scan');
        Route::post('/tickets', [TicketAdminController::class, 'store'])->name('tickets.store');
        Route::put('/tickets/{ticket}', [TicketAdminController::class, 'update'])->name('tickets.update');
        Route::delete('/tickets/{ticket}', [TicketAdminController::class, 'destroy'])->name('tickets.destroy');
        Route::post('/tickets/redeem', [TicketAdminController::class, 'redeem'])->name('tickets.redeem');
    });

Route::middleware(['auth','verified'])->get('/dashboard', DashboardController::class)->name('dashboard');

require __DIR__.'/auth.php';
require __DIR__.'/settings.php';
