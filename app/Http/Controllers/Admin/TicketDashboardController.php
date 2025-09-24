<?php

namespace App\Http\Controllers\Admin;

use App\Enums\TicketStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreTicketRequest;
use App\Http\Requests\Admin\UpdateTicketRequest;
use App\Models\{Ticket, TicketCheckin, User};
use App\Services\TicketService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class TicketDashboardController extends Controller
{
    public function __construct(private TicketService $tickets)
    {
    }

    public function index(): Response
    {
        $statusCounts = Ticket::query()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $totalTickets = (int) $statusCounts->sum();
        $cancelled = (int) ($statusCounts[TicketStatus::cancelled->value] ?? 0);
        $redeemed = (int) ($statusCounts[TicketStatus::redeemed->value] ?? 0);

        $statusBreakdown = collect(TicketStatus::cases())
            ->map(function (TicketStatus $status) use ($statusCounts, $totalTickets) {
                $value = $status->value;
                $count = (int) ($statusCounts[$value] ?? 0);

                return [
                    'status' => $value,
                    'count' => $count,
                    'percentage' => $totalTickets > 0 ? round(($count / $totalTickets) * 100, 1) : 0,
                ];
            })
            ->values();

        $recentTickets = Ticket::query()
            ->with('user:id,name')
            ->latest()
            ->limit(8)
            ->get()
            ->map(fn (Ticket $ticket) => [
                'id' => $ticket->id,
                'code' => $ticket->code,
                'status' => $ticket->status instanceof \UnitEnum ? $ticket->status->value : $ticket->status,
                'owner_name' => $ticket->user?->name,
                'issued_at' => optional($ticket->created_at)?->toDateTimeString(),
                'redeemed_at' => optional($ticket->redeemed_at)?->toDateTimeString(),
            ])
            ->values();

        $tickets = Ticket::query()
            ->with('user:id,name,email')
            ->latest()
            ->limit(20)
            ->get()
            ->map(fn (Ticket $ticket) => [
                'id' => $ticket->id,
                'code' => $ticket->code,
                'status' => $ticket->status instanceof \UnitEnum ? $ticket->status->value : $ticket->status,
                'owner' => $ticket->user ? [
                    'id' => $ticket->user->id,
                    'name' => $ticket->user->name,
                    'email' => $ticket->user->email,
                ] : null,
                'issued_at' => optional($ticket->created_at)?->toDateTimeString(),
                'redeemed_at' => optional($ticket->redeemed_at)?->toDateTimeString(),
                'qr_code_hash' => $ticket->qr_code_hash,
                'pdf_url' => $ticket->pdf_url,
            ])
            ->values();

        $users = User::query()
            ->select(['id', 'name', 'email'])
            ->orderBy('name')
            ->get()
            ->values();

        $statusOptions = array_column(TicketStatus::cases(), 'value');

        $recentCheckins = TicketCheckin::query()
            ->with(['ticket:id', 'ticket.user:id,name', 'scannedBy:id,name'])
            ->latest('scanned_at')
            ->limit(5)
            ->get()
            ->map(fn (TicketCheckin $checkin) => [
                'id' => $checkin->id,
                'ticket_code' => $checkin->ticket?->code,
                'scanner_name' => $checkin->scannedBy?->name,
                'scanned_at' => optional($checkin->scanned_at)?->toDateTimeString(),
                'location' => $checkin->location,
                'device' => $checkin->device,
            ])
            ->values();

        return Inertia::render('tickets/admin-dashboard', [
            'stats' => [
                'total' => $totalTickets,
                'active' => $totalTickets - $cancelled,
                'redeemed' => $redeemed,
                'cancelled' => $cancelled,
            ],
            'statusBreakdown' => $statusBreakdown,
            'recentTickets' => $recentTickets,
            'tickets' => $tickets,
            'users' => $users,
            'statusOptions' => $statusOptions,
            'recentCheckins' => $recentCheckins,
        ]);
    }

    public function store(StoreTicketRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $ticket = $this->tickets->issue($validated['order_item_id'], $validated['user_id'] ?? null);

        $status = TicketStatus::from($validated['status']);
        if ($status !== TicketStatus::issued) {
            $ticket->update(['status' => $status]);
        }

        return redirect()->route('tickets.admin')->with('success', 'Ticket created successfully.');
    }

    public function update(UpdateTicketRequest $request, Ticket $ticket): RedirectResponse
    {
        $validated = $request->validated();

        $status = TicketStatus::from($validated['status']);

        $updateData = [
            'user_id' => $validated['user_id'] ?? null,
            'status' => $status,
        ];

        if ($status === TicketStatus::redeemed) {
            $updateData['redeemed_at'] = $ticket->redeemed_at ?? now();
        } else {
            $updateData['redeemed_at'] = null;
        }

        $ticket->update($updateData);

        return redirect()->route('tickets.admin')->with('success', 'Ticket updated successfully.');
    }

    public function destroy(Ticket $ticket): RedirectResponse
    {
        $ticket->delete();

        return redirect()->route('tickets.admin')->with('success', 'Ticket deleted successfully.');
    }
}
