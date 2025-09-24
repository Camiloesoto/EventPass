<?php

use Illuminate\Support\Facades\Route;
use App\Enums\EventStatus;
use App\Http\Controllers\{
    DashboardController,
    EventController,
    OrderController,
    PaymentController,
    StripeWebhookController,
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

Route::resource('events', EventController::class);

Route::middleware(['auth'])->group(function () {
    Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
    Route::get('/orders/{order}/checkout', [OrderController::class, 'checkout'])->name('orders.checkout');

    Route::post('/orders/{order}/payments', [PaymentController::class, 'store'])->name('orders.payments.store');
    Route::post('/orders/{order}/pay/stripe', [PaymentController::class, 'stripeCheckout'])->name('orders.payments.stripe.checkout');
    Route::get('/orders/{order}/pay/stripe/success', [PaymentController::class, 'stripeSuccess'])->name('orders.payments.stripe.success');
    Route::get('/orders/{order}/pay/stripe/cancel', [PaymentController::class, 'stripeCancel'])->name('orders.payments.stripe.cancel');
});

Route::post('/events/{event}/waitlist', [WaitlistController::class, 'store'])
    ->middleware('auth')
    ->name('events.waitlist.store');

Route::post('/stripe/webhook', [StripeWebhookController::class, 'handle'])
    ->name('stripe.webhook')
    ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);

Route::middleware(['auth','verified'])->get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

require __DIR__.'/auth.php';
require __DIR__.'/settings.php';
