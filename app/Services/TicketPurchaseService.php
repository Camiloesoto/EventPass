<?php

namespace App\Services;

use App\Models\Event;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Ticket;
use App\Models\TicketType;
use App\Models\User;
use App\Enums\OrderStatus;
use App\Enums\TicketStatus;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TicketPurchaseService
{
    public function purchaseTickets(array $ticketTypes, int $eventId): Order
    {
        DB::beginTransaction();
        
        try {
            $event = Event::findOrFail($eventId);
            $order = $this->createOrder();
            $totalAmount = $this->processTicketTypes($ticketTypes, $order);
            
            $this->updateOrderTotals($order, $totalAmount);
            DB::commit();
            
            return $order;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function transferTicket(int $ticketId, string $email): Ticket
    {
        $ticket = Ticket::where('user_id', Auth::id())->findOrFail($ticketId);
        $newUser = User::where('email', $email)->firstOrFail();

        $ticket->setUserId($newUser->getId());
        $ticket->setStatus(TicketStatus::transferred);
        $ticket->save();

        return $ticket;
    }

    private function createOrder(): Order
    {
        return Order::create([
            'user_id' => Auth::id(),
            'order_date' => now(),
            'status' => OrderStatus::pending,
            'subtotal_amount' => 0,
            'discount_amount' => 0,
            'total_amount' => 0,
        ]);
    }

    private function processTicketTypes(array $ticketTypes, Order $order): float
    {
        $totalAmount = 0;

        foreach ($ticketTypes as $ticketTypeData) {
            $quantity = (int) $ticketTypeData['quantity'];
            
            if ($quantity <= 0) {
                continue;
            }
            
            $ticketType = TicketType::findOrFail($ticketTypeData['id']);

            if ($ticketType->getQuantity() < $quantity) {
                throw new \Exception(__('events.not_enough_tickets', ['type' => $ticketType->getName()]));
            }

            $subtotal = $ticketType->getPrice() * $quantity;
            $totalAmount += $subtotal;

            $orderItem = OrderItem::create([
                'order_id' => $order->getId(),
                'ticket_type_id' => $ticketType->getId(),
                'quantity' => $quantity,
                'unit_price' => $ticketType->getPrice(),
            ]);

            $this->createIndividualTickets($orderItem, $quantity, $order->getId());
            $this->updateTicketTypeQuantity($ticketType, $quantity);
        }

        return $totalAmount;
    }

    private function createIndividualTickets(OrderItem $orderItem, int $quantity, int $orderId): void
    {
        for ($i = 0; $i < $quantity; $i++) {
            $code = $this->generateUniqueTicketCode($orderId, $i + 1);
            
            Ticket::create([
                'order_item_id' => $orderItem->getId(),
                'user_id' => Auth::id(),
                'code' => $code,
                'qr_code_hash' => hash('sha256', uniqid((string) $orderId, true)),
                'pdf_url' => '#',
                'status' => TicketStatus::issued,
            ]);
        }
    }

    private function updateTicketTypeQuantity(TicketType $ticketType, int $quantity): void
    {
        $ticketType->setQuantity($ticketType->getQuantity() - $quantity);
        $ticketType->save();
    }

    private function updateOrderTotals(Order $order, float $totalAmount): void
    {
        $order->setSubtotalAmount($totalAmount);
        $order->setTotalAmount($totalAmount);
        $order->setStatus(OrderStatus::paid);
        $order->save();
    }

    private function generateUniqueTicketCode(int $orderId, int $ticketNumber): string
    {
        $baseCode = 'TK' . str_pad($orderId, 6, '0', STR_PAD_LEFT) . str_pad($ticketNumber, 3, '0', STR_PAD_LEFT);
        
        $counter = 0;
        $code = $baseCode;
        
        while (Ticket::where('code', $code)->exists()) {
            $counter++;
            $code = $baseCode . '-' . str_pad($counter, 2, '0', STR_PAD_LEFT);
        }
        
        return $code;
    }
}
