<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Ticket;
use App\Models\TicketType;
use App\Models\User;
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
            'ticket_types.*.quantity' => 'required|integer|min:0|max:10',
        ]);

        // Custom validation: at least one ticket type must have quantity > 0
        $hasValidQuantity = false;
        foreach ($request->ticket_types as $ticketTypeData) {
            if ((int) $ticketTypeData['quantity'] > 0) {
                $hasValidQuantity = true;
                break;
            }
        }

        if (!$hasValidQuantity) {
            return back()->withErrors(['ticket_types' => 'Please select at least one ticket to purchase.']);
        }

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
            $hasValidTickets = false;

            // Create order items
            foreach ($request->ticket_types as $ticketTypeData) {
                $quantity = (int) $ticketTypeData['quantity'];
                
                // Skip if quantity is 0
                if ($quantity <= 0) {
                    continue;
                }
                
                $ticketType = TicketType::find($ticketTypeData['id']);

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
                    // Generate unique code
                    $code = $this->generateUniqueTicketCode($order->getId(), $i + 1);
                    
                    Ticket::create([
                        'order_item_id' => $orderItem->getId(),
                        'user_id' => Auth::id(),
                        'code' => $code,
                        'qr_code_hash' => hash('sha256', uniqid((string) $order->getId(), true)),
                        'pdf_url' => '#',
                        'status' => TicketStatus::issued,
                    ]);
                }

                // Update ticket type quantity
                $ticketType->setQuantity($ticketType->getQuantity() - $quantity);
                $ticketType->save();
                
                $hasValidTickets = true;
            }

            // Check if any tickets were selected
            if (!$hasValidTickets) {
                throw new \Exception("Please select at least one ticket to purchase.");
            }

            // Update order totals
            $order->setSubtotalAmount($totalAmount);
            $order->setTotalAmount($totalAmount);
            $order->setStatus(OrderStatus::paid);
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
        // Return a beautiful QR code view
        return view('tickets.qr', compact('ticket'));
    }

    public function download(Ticket $ticket)
    {
        // For now, return a simple text response
        // Later you can implement actual PDF generation
        $content = "Ticket: {$ticket->getCode()}\n";
        $content .= "Event: {$ticket->orderItem->ticketType->event->getName()}\n";
        $content .= "Type: {$ticket->orderItem->ticketType->getName()}\n";
        $content .= "Price: $" . number_format($ticket->orderItem->getUnitPrice(), 2) . "\n";
        $content .= "Status: {$ticket->getStatus()->value}\n";
        $content .= "QR Code: {$ticket->getQrCodeHash()}\n";

        return response($content)
            ->header('Content-Type', 'text/plain')
            ->header('Content-Disposition', 'attachment; filename="ticket-' . $ticket->getCode() . '.txt"');
    }

    public function transfer(Request $request, Ticket $ticket)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        try {
            // Find the user to transfer to
            $newUser = User::where('email', $request->email)->first();
            
            if (!$newUser) {
                return back()->withErrors(['email' => 'User not found.']);
            }

            // Transfer the ticket
            $ticket->setUserId($newUser->getId());
            $ticket->setStatus(TicketStatus::transferred);
            $ticket->save();

            Log::info('Ticket transferred', [
                'ticket_id' => $ticket->getId(),
                'from_user_id' => Auth::id(),
                'to_user_id' => $newUser->getId(),
                'to_email' => $request->email,
            ]);

            return redirect()->route('tickets.index')
                ->with('success', 'Ticket transferred successfully to ' . $newUser->getName() . '!');

        } catch (\Exception $e) {
            Log::error('Ticket transfer failed', [
                'ticket_id' => $ticket->getId(),
                'from_user_id' => Auth::id(),
                'to_email' => $request->email,
                'error' => $e->getMessage(),
            ]);

            return back()->withErrors(['email' => 'Failed to transfer ticket: ' . $e->getMessage()]);
        }
    }

    private function generateUniqueTicketCode(int $orderId, int $ticketNumber): string
    {
        $baseCode = 'TK' . str_pad($orderId, 6, '0', STR_PAD_LEFT) . str_pad($ticketNumber, 3, '0', STR_PAD_LEFT);
        
        // Check if code already exists
        $counter = 0;
        $code = $baseCode;
        
        while (Ticket::where('code', $code)->exists()) {
            $counter++;
            $code = $baseCode . '-' . str_pad($counter, 2, '0', STR_PAD_LEFT);
        }
        
        return $code;
    }
}
