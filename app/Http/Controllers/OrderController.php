<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\TicketType;
use App\Http\Requests\StoreOrderRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    public function index()
    {
        $orders = auth()->user()->orders()
            ->with(['items.ticketType.event'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('orders.index', compact('orders'));
    }

    public function store(StoreOrderRequest $request)
    {
        try {
            DB::beginTransaction();

            $order = Order::create([
                'user_id' => auth()->id(),
                'order_date' => now(),
                'status' => \App\Enums\OrderStatus::pending,
                'subtotal_amount' => 0,
                'discount_amount' => 0,
                'total_amount' => 0,
            ]);

            $subtotal = 0;

            foreach ($request->validated()['items'] as $item) {
                $ticketType = TicketType::findOrFail($item['ticket_type_id']);
                
                $orderItem = OrderItem::create([
                    'order_id' => $order->getId(),
                    'ticket_type_id' => $ticketType->getId(),
                    'quantity' => $item['quantity'],
                    'unit_price' => $ticketType->getPrice(),
                ]);

                $subtotal += $orderItem->getQuantity() * $orderItem->getUnitPrice();
            }

            $order->setSubtotalAmount($subtotal);
            $order->setTotalAmount($subtotal);
            $order->save();

            DB::commit();

            Log::info('Order created', [
                'order_id' => $order->getId(),
                'user_id' => auth()->id(),
                'total_amount' => $order->getTotalAmount(),
            ]);

            return redirect()->route('orders.checkout', $order)
                ->with('success', 'Order created successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Error creating order', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
            ]);

            return back()->withInput()
                ->with('error', 'Error creating order. Please try again.');
        }
    }

    public function checkout(Order $order)
    {
        $order->load(['items.ticketType.event', 'user']);
        
        return view('orders.checkout', compact('order'));
    }

    public function show(Order $order)
    {
        $order->load(['items.ticketType.event', 'payments']);
        
        return view('orders.show', compact('order'));
    }
}