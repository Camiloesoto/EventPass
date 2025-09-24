<?php
namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Http\Requests\StorePaymentRequest;
use App\Models\{Order, Payment};
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Jobs\GenerateTicketArtifacts;

class PaymentController extends Controller
{
    public function store(Order $order, StorePaymentRequest $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validated();

        $result = DB::transaction(function () use ($order, $validated) {
            $payment = Payment::create([
                'order_id' => $order->id,
                'provider' => $validated['provider'],
                'provider_payment_id' => $validated['provider_payment_id'],
                'amount' => $validated['amount'],
                'currency' => strtoupper($validated['currency']),
                'status' => $validated['status'] ?? PaymentStatus::pending,
                'paid_at' => isset($validated['paid_at']) ? $validated['paid_at'] : (isset($validated['status']) && $validated['status'] === PaymentStatus::succeeded->value ? now() : null),
            ]);

            $tickets = [];
            if ($payment->status === PaymentStatus::succeeded) {
                $order->status = OrderStatus::paid;
                $order->save();

                $order->load('items');
                $tickets = $order->generateTickets();
                // Generate artifacts after commit
                DB::afterCommit(function () use ($tickets) {
                    foreach ($tickets as $ticket) {
                        GenerateTicketArtifacts::dispatch($ticket->id);
                    }
                });
            }

            return compact('payment','tickets');
        });

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'payment' => $result['payment'],
                'tickets' => $result['tickets'],
            ], 201);
        }

        return redirect()
            ->route('tickets.index')
            ->with('status', 'Payment successful. Your tickets are ready.');
    }

    public function stripeCheckout(Order $order, Request $request): RedirectResponse
    {
        $order->load('items.ticketType');

        // Fallback if Stripe SDK is not installed
        $stripeClientClass = 'Stripe\\StripeClient';
        if (!class_exists($stripeClientClass)) {
            return redirect()
                ->route('orders.checkout', $order)
                ->with('status', 'Stripe SDK not installed. Use test payment button for now.');
        }

        $stripe = new $stripeClientClass(config('services.stripe.secret'));

        $lineItems = [];
        foreach ($order->items as $item) {
            $lineItems[] = [
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => ['name' => $item->ticketType?->name ?? 'Ticket'],
                    'unit_amount' => (int) round(((float) $item->unit_price) * 100),
                ],
                'quantity' => (int) $item->quantity,
            ];
        }

        $session = $stripe->checkout->sessions->create([
            'mode' => 'payment',
            'success_url' => route('orders.payments.stripe.success', $order) . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('orders.payments.stripe.cancel', $order),
            'line_items' => $lineItems,
            'metadata' => [
                'order_id' => (string) $order->id,
            ],
        ]);

        return redirect()->away($session->url);
    }

    public function stripeSuccess(Order $order, Request $request): RedirectResponse
    {
        $sessionId = (string) $request->query('session_id', '');

        // If Stripe SDK missing, treat as paid for test purposes
        $stripeClientClass = 'Stripe\\StripeClient';
        if (!class_exists($stripeClientClass)) {
            $payment = Payment::create([
                'order_id' => $order->id,
                'provider' => 'stripe',
                'provider_payment_id' => $sessionId ?: ('test_' . uniqid()),
                'amount' => $order->total_amount,
                'currency' => 'USD',
                'status' => PaymentStatus::succeeded,
                'paid_at' => now(),
            ]);

            $order->status = OrderStatus::paid;
            $order->save();
            $order->load('items');
            $tickets = $order->generateTickets();
            DB::afterCommit(function () use ($tickets) {
                foreach ($tickets as $ticket) {
                    GenerateTicketArtifacts::dispatch($ticket->id);
                }
            });

            return redirect()->route('tickets.index')->with('status', 'Payment successful. Your tickets are ready.');
        }

        $stripe = new $stripeClientClass(config('services.stripe.secret'));
        if ($sessionId) {
            try {
                $session = $stripe->checkout->sessions->retrieve($sessionId);
                if ($session && $session->payment_status === 'paid') {
                    Payment::create([
                        'order_id' => $order->id,
                        'provider' => 'stripe',
                        'provider_payment_id' => $sessionId,
                        'amount' => $order->total_amount,
                        'currency' => strtoupper($session->currency ?? 'usd'),
                        'status' => PaymentStatus::succeeded,
                        'paid_at' => now(),
                    ]);

                    $order->status = OrderStatus::paid;
                    $order->save();
                    $order->load('items');
                    $tickets = $order->generateTickets();
                    DB::afterCommit(function () use ($tickets) {
                        foreach ($tickets as $ticket) {
                            GenerateTicketArtifacts::dispatch($ticket->id);
                        }
                    });

                    return redirect()->route('tickets.index')->with('status', 'Payment successful. Your tickets are ready.');
                }
            } catch (\Throwable $e) {
                // fallthrough to cancel
            }
        }

        return redirect()->route('orders.checkout', $order)->withErrors(['payment' => 'Payment not completed.']);
    }

    public function stripeCancel(Order $order): RedirectResponse
    {
        return redirect()->route('orders.checkout', $order)->withErrors(['payment' => 'Payment cancelled.']);
    }
}
