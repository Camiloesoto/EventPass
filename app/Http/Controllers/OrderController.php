<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Http\Requests\StoreOrderRequest;
use App\Models\{Order, OrderItem, TicketType};
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(): JsonResponse
    {
        $orders = Order::query()
            ->with(['items.ticketType:id,event_id,name,price', 'payments'])
            ->latest('order_date')
            ->paginate(10);

        return response()->json([
            'success' => true,
            'orders' => $orders,
        ]);
    }

    public function store(StoreOrderRequest $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validated();

        // Build items array (support both API payload and HTML form posts)
        $items = collect($validated['items'] ?? [])
            ->filter(fn ($i) => isset($i['ticket_type_id']) && (int)($i['quantity'] ?? 0) > 0)
            ->values()
            ->all();

        if (empty($items)) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'No items selected'], 422);
            }
            return back()->withErrors(['items' => 'Please select at least one ticket.'])->withInput();
        }

        $user = $request->user();
        if (!$user) {
            // Should be behind auth middleware, but handle just in case
            return $request->wantsJson()
                ? response()->json(['success' => false, 'message' => 'Unauthorized'], 401)
                : redirect()->route('login');
        }

        // Enforce availability per ticket type
        $errors = [];
        $resolvedItems = [];
        foreach ($items as $index => $item) {
            $ticketType = TicketType::query()->select(['id', 'name', 'price', 'quantity'])->findOrFail($item['ticket_type_id']);
            $requested = (int) $item['quantity'];
            $available = (int) $ticketType->available_quantity;
            if ($requested > $available) {
                $errors["items.$index.quantity"] = "Only $available left for {$ticketType->name}.";
            } else {
                $resolvedItems[] = [
                    'ticketType' => $ticketType,
                    'quantity' => $requested,
                    'unit_price' => isset($item['unit_price']) ? (float) $item['unit_price'] : (float) $ticketType->price,
                ];
            }
        }

        if (!empty($errors)) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'errors' => $errors], 422);
            }
            return back()->withErrors($errors)->withInput();
        }

        $order = DB::transaction(function () use ($validated, $resolvedItems, $user) {
            $order = Order::create([
                'user_id' => $user->id,
                'order_date' => now(),
                'status' => OrderStatus::pending,
                'discount_amount' => (float) ($validated['discount_amount'] ?? 0),
                'subtotal_amount' => 0,
                'total_amount' => 0,
            ]);

            foreach ($resolvedItems as $ri) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'ticket_type_id' => $ri['ticketType']->id,
                    'quantity' => (int) $ri['quantity'],
                    'unit_price' => (float) $ri['unit_price'],
                ]);
            }

            $order->load('items');
            $order->calculateTotals();
            $order->save();

            return $order->fresh(['items.ticketType']);
        });

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'order' => $order,
                'next' => [
                    'checkout' => route('orders.checkout', ['order' => $order->id]),
                ],
            ], 201);
        }

        return redirect()->route('orders.checkout', $order);
    }

    public function checkout(Order $order): View
    {
        $order->load('items.ticketType');
        return view('orders.checkout', ['order' => $order]);
    }

    public function show(Order $order): JsonResponse
    {
        $order->load(['items.ticketType', 'payments']);

        return response()->json([
            'success' => true,
            'order' => $order,
        ]);
    }
}
