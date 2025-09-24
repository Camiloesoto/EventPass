<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Jobs\GenerateTicketArtifacts;
use App\Models\{Order, Payment};
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StripeWebhookController extends Controller
{
    public function handle(Request $request): Response
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $secret = (string) config('services.stripe.webhook_secret', env('STRIPE_WEBHOOK_SECRET'));

        // If SDK not installed, accept payload for local/test
        $stripeWebhookClass = 'Stripe\\Webhook';
        if (!class_exists($stripeWebhookClass)) {
            return $this->handleWithoutSdk($payload);
        }

        try {
            $event = $stripeWebhookClass::constructEvent($payload, $sigHeader, $secret);
        } catch (\Throwable $e) {
            Log::warning('Stripe webhook signature verification failed', ['error' => $e->getMessage()]);
            return response('Invalid signature', 400);
        }

        return $this->processEvent($event);
    }

    private function handleWithoutSdk(string $payload): Response
    {
        // In local dev without Stripe SDK, skip signature validation and parse JSON
        $data = json_decode($payload, true);
        if (!is_array($data)) {
            return response('Bad payload', 400);
        }
        return $this->processEvent((object) $data);
    }

    private function processEvent(object $event): Response
    {
        $type = $event->type ?? ($event['type'] ?? null);

        if ($type === 'checkout.session.completed') {
            $session = $event->data['object'] ?? $event->data->object ?? [];
            $orderId = (int) ($session['metadata']['order_id'] ?? 0);
            if (!$orderId) {
                return response('No order_id in metadata', 200);
            }

            /** @var Order $order */
            $order = Order::query()->with('items')->find($orderId);
            if (!$order) {
                return response('Order not found', 200);
            }

            if ($order->status === OrderStatus::paid) {
                return response('Already paid', 200);
            }

            DB::transaction(function () use ($order, $session) {
                $amount = isset($session['amount_total']) ? ((float) $session['amount_total']) / 100.0 : $order->total_amount;
                $currency = strtoupper((string) ($session['currency'] ?? 'USD'));
                $providerPaymentId = (string) ($session['id'] ?? ('sess_' . uniqid()));

                Payment::firstOrCreate(
                    ['provider_payment_id' => $providerPaymentId],
                    [
                        'order_id' => $order->id,
                        'provider' => 'stripe',
                        'amount' => $amount,
                        'currency' => $currency,
                        'status' => PaymentStatus::succeeded,
                        'paid_at' => now(),
                    ]
                );

                $order->status = OrderStatus::paid;
                $order->save();

                $tickets = $order->generateTickets();

                DB::afterCommit(function () use ($tickets) {
                    foreach ($tickets as $ticket) {
                        GenerateTicketArtifacts::dispatch($ticket->id);
                    }
                });
            });
        }

        // Acknowledge all events
        return response('ok', 200);
    }
}
