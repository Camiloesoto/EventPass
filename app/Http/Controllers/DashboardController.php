<?php

namespace App\Http\Controllers;

use App\Enums\{EventStatus, OrderStatus, PaymentStatus, TicketStatus};
use App\Models\{Event, Order, Payment, Ticket};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $user = $request->user();
        $now = now();

        $eventsUpcoming = Event::query()
            ->where('status', EventStatus::published->value)
            ->where('start_time', '>=', $now)
            ->count();

        $ordersTotal = Order::query()->count();
        $ordersPaid = Order::query()->where('status', OrderStatus::paid->value)->count();

        $ticketsTotal = Ticket::query()->count();
        $ticketsRedeemed = Ticket::query()->where('status', TicketStatus::redeemed->value)->count();
        $ticketsCancelled = Ticket::query()->where('status', TicketStatus::cancelled->value)->count();
        $ticketsActive = max(0, $ticketsTotal - $ticketsRedeemed - $ticketsCancelled);

        $paymentsTotalAmount = (float) Payment::query()
            ->where('status', PaymentStatus::succeeded->value)
            ->sum('amount');
        $paymentsLast7Days = (float) Payment::query()
            ->where('status', PaymentStatus::succeeded->value)
            ->where('paid_at', '>=', $now->copy()->subDays(7))
            ->sum('amount');

        $myTickets = Ticket::query()->where('user_id', $user->id)->count();
        $myOrders = Order::query()->where('user_id', $user->id)->count();

        $isAdmin = Gate::allows('access-admin');

        return Inertia::render('dashboard', [
            'metrics' => [
                'events_upcoming' => $eventsUpcoming,
                'orders_total' => $ordersTotal,
                'orders_paid' => $ordersPaid,
                'tickets_total' => $ticketsTotal,
                'tickets_active' => $ticketsActive,
                'tickets_redeemed' => $ticketsRedeemed,
                'payments_total_amount' => $paymentsTotalAmount,
                'payments_last_7_days' => $paymentsLast7Days,
                'my_tickets' => $myTickets,
                'my_orders' => $myOrders,
            ],
            'isAdmin' => $isAdmin,
            'links' => [
                'events' => route('events.index'),
                'myTickets' => route('tickets.index'),
                'adminTickets' => $isAdmin ? route('admin.tickets.index') : null,
            ],
        ]);
    }
}
