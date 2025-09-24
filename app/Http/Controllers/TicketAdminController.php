<?php

namespace App\Http\Controllers;

use App\Enums\TicketStatus;
use App\Models\{Ticket, TicketCheckin, User};
use App\Services\TicketService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class TicketAdminController extends Controller
{
    public function __construct(private TicketService $svc) {}

    public function index(Request $request): InertiaResponse
    {
        // Stats
        $total = (int) Ticket::count();
        $redeemed = (int) Ticket::where('status', TicketStatus::redeemed)->count();
        $cancelled = (int) Ticket::where('status', TicketStatus::cancelled)->count();
        $active = $total - $redeemed - $cancelled;

        $statusCounts = Ticket::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->all();

        $statusBreakdown = [];
        foreach (TicketStatus::cases() as $case) {
            $count = (int) ($statusCounts[$case->value] ?? 0);
            $percentage = $total > 0 ? round(($count / $total) * 100, 1) : 0.0;
            $statusBreakdown[] = [
                'status' => $case->value,
                'count' => $count,
                'percentage' => $percentage,
            ];
        }

        // Tickets list (basic, newest first)
        $tickets = Ticket::query()
            ->with(['user:id,name,email'])
            ->latest('id')
            ->take(100)
            ->get()
            ->map(function (Ticket $t) {
                return [
                    'id' => $t->id,
                    'code' => (string) $t->code,
                    'status' => $t->status instanceof \UnitEnum ? $t->status->value : (string) $t->status,
                    'owner' => $t->user ? [
                        'id' => $t->user->id,
                        'name' => $t->user->name,
                        'email' => $t->user->email,
                    ] : null,
                    'issued_at' => optional($t->created_at)?->toDateTimeString(),
                    'redeemed_at' => optional($t->redeemed_at)?->toDateTimeString(),
                    'qr_code_hash' => (string) $t->qr_code_hash,
                    'pdf_url' => (string) $t->pdf_url,
                ];
            });

        // Users list (for assignment in UI)
        $users = User::query()->select(['id','name','email'])->orderBy('name')->take(200)->get();

        // Recent tickets & checkins
        $recentTickets = Ticket::query()
            ->with('user:id,name')
            ->latest('id')
            ->take(10)
            ->get()
            ->map(fn (Ticket $t) => [
                'id' => $t->id,
                'code' => (string) $t->code,
                'status' => $t->status instanceof \UnitEnum ? $t->status->value : (string) $t->status,
                'owner_name' => $t->user?->name,
                'issued_at' => optional($t->created_at)?->toDateTimeString(),
                'redeemed_at' => optional($t->redeemed_at)?->toDateTimeString(),
            ]);

        $recentCheckins = TicketCheckin::query()
            ->with(['ticket:id,code','scannedBy:id,name'])
            ->latest('scanned_at')
            ->take(10)
            ->get()
            ->map(fn (TicketCheckin $c) => [
                'id' => $c->id,
                'ticket_code' => $c->ticket?->code,
                'scanner_name' => $c->scannedBy?->name,
                'scanned_at' => optional($c->scanned_at)?->toDateTimeString(),
                'location' => $c->location,
                'device' => $c->device,
            ]);

        return Inertia::render('tickets/admin-dashboard', [
            'stats' => compact('total','active','redeemed','cancelled'),
            'statusBreakdown' => $statusBreakdown,
            'tickets' => $tickets,
            'users' => $users,
            'statusOptions' => array_map(fn ($c) => $c->value, TicketStatus::cases()),
            'recentTickets' => $recentTickets,
            'recentCheckins' => $recentCheckins,
        ]);
    }

    public function scan(): InertiaResponse
    {
        return Inertia::render('tickets/admin-scan', [
            'redeemRoute' => route('admin.tickets.redeem'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'order_item_id' => ['required','integer','exists:order_items,id'],
            'user_id' => ['nullable','integer','exists:users,id'],
            'status' => ['nullable', Rule::in(array_map(fn ($c) => $c->value, TicketStatus::cases()))],
        ]);

        $ticket = $this->svc->issue((int) $data['order_item_id'], $data['user_id'] ?? null);

        if (!empty($data['status'])) {
            $ticket->update(['status' => TicketStatus::from($data['status'])]);
        }

        return back();
    }

    public function update(Request $request, Ticket $ticket): RedirectResponse
    {
        $data = $request->validate([
            'user_id' => ['nullable','integer','exists:users,id'],
            'status' => ['nullable', Rule::in(array_map(fn ($c) => $c->value, TicketStatus::cases()))],
        ]);

        $updates = [];
        if (array_key_exists('user_id', $data)) {
            $updates['user_id'] = $data['user_id'];
        }
        if (!empty($data['status'])) {
            $updates['status'] = TicketStatus::from($data['status']);
        }
        if ($updates) {
            $ticket->update($updates);
        }

        return back();
    }

    public function destroy(Ticket $ticket): RedirectResponse
    {
        $ticket->delete();
        return back();
    }

    public function redeem(Request $request)
    {
        $payload = $request->validate([
            'qr_code_hash' => ['required','string','max:64'],
        ]);

        $scannerId = optional($request->user())->id;
        $device = $request->header('X-Device');
        $location = $request->header('X-Location');

        $ticket = $this->svc->redeemByIdentifier($payload['qr_code_hash'], $scannerId, $device, $location);

        return response()->json([
            'id' => $ticket->id,
            'code' => (string) $ticket->code,
            'status' => $ticket->status instanceof \UnitEnum ? $ticket->status->value : (string) $ticket->status,
            'redeemed_at' => optional($ticket->redeemed_at)?->toDateTimeString(),
            'user_id' => $ticket->user_id,
            'meta' => null,
        ]);
    }
}
