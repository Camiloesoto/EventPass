<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Ticket;
use App\Models\TicketType;
use App\Enums\OrderStatus;
use App\Enums\TicketStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TicketController extends Controller
{
    public function index()
    {
        $tickets = Auth::user()->tickets()
            ->with(['orderItem.ticketType.event'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('tickets.index', compact('tickets'));
    }

    public function purchase(Request $request, Event $event)
    {
        $request->validate([
            'ticket_types' => 'required|array|min:1',
            'ticket_types.*.id' => 'required|exists:ticket_types,id',
            'ticket_types.*.quantity' => 'required|integer|min:1|max:10',
        ]);

        try {
            DB::beginTransaction();

            // Create order
            $order = Order::create([
                'user_id' => Auth::id(),
                'order_date' => now(),
                'status' => OrderStatus::pending,
                'subtotal_amount' => 0,
                'discount_amount' => 0,
                'total_amount' => 0,
            ]);

            $totalAmount = 0;

            // Create order items
            foreach ($request->ticket_types as $ticketTypeData) {
                $ticketType = TicketType::find($ticketTypeData['id']);
                $quantity = $ticketTypeData['quantity'];

                // Check availability
                if ($ticketType->getQuantity() < $quantity) {
                    throw new \Exception("Not enough tickets available for {$ticketType->getName()}");
                }

                $subtotal = $ticketType->getPrice() * $quantity;
                $totalAmount += $subtotal;

                $orderItem = OrderItem::create([
                    'order_id' => $order->getId(),
                    'ticket_type_id' => $ticketType->getId(),
                    'quantity' => $quantity,
                    'unit_price' => $ticketType->getPrice(),
                ]);

                // Create individual tickets
                for ($i = 0; $i < $quantity; $i++) {
                    Ticket::create([
                        'order_item_id' => $orderItem->getId(),
                        'user_id' => Auth::id(),
                        'code' => 'TK' . str_pad($order->getId(), 6, '0', STR_PAD_LEFT) . str_pad($i + 1, 3, '0', STR_PAD_LEFT),
                        'qr_code_hash' => hash('sha256', uniqid((string) $order->getId(), true)),
                        'pdf_url' => '#',
                        'status' => TicketStatus::issued,
                    ]);
                }

                // Update ticket type quantity
                $ticketType->setQuantity($ticketType->getQuantity() - $quantity);
                $ticketType->save();
            }

            // Update order totals
            $order->setSubtotalAmount($totalAmount);
            $order->setTotalAmount($totalAmount);
            $order->setStatus(OrderStatus::completed);
            $order->save();

            DB::commit();

            Log::info('Ticket purchase completed', [
                'user_id' => Auth::id(),
                'order_id' => $order->getId(),
                'event_id' => $event->getId(),
                'total_amount' => $totalAmount,
            ]);

            return redirect()->route('tickets.index')
                ->with('success', 'Tickets purchased successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Ticket purchase failed', [
                'user_id' => Auth::id(),
                'event_id' => $event->getId(),
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Failed to purchase tickets: ' . $e->getMessage());
        }
    }

    public function showQr(Ticket $ticket)
    {
        // Simple QR code generation (you can enhance this later)
        $qrData = [
            'ticket_id' => $ticket->getId(),
            'user_id' => $ticket->getUserId(),
            'event' => $ticket->orderItem->ticketType->event->getName(),
            'hash' => $ticket->getQrCodeHash(),
        ];

        return response()->json($qrData);
    }
}
