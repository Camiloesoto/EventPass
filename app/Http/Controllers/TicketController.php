<?php

namespace App\Http\Controllers;

use App\Http\Requests\{TicketIssueRequest, TicketTransferRequest};
use App\Models\{Ticket, User};
use App\Services\{QrCodeGenerator, TicketPdfService, TicketService};
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TicketController extends Controller
{
    use AuthorizesRequests;

    public function __construct(private TicketService $svc, private TicketPdfService $pdfs, private QrCodeGenerator $qr) {}

    public function index(Request $request): Response
    {
        $user = $request->user();

        $tickets = $user->tickets()
            ->with(['checkins' => fn ($query) => $query->latest('scanned_at')])
            ->latest()
            ->get()
            ->map(function (Ticket $ticket) {
                $latestCheckin = $ticket->checkins->first();

                return [
                    'id' => $ticket->id,
                    'code' => $ticket->code,
                    'status' => $ticket->status instanceof \UnitEnum ? $ticket->status->value : $ticket->status,
                    'issued_at' => optional($ticket->created_at)?->toDateTimeString(),
                    'redeemed_at' => optional($ticket->redeemed_at)?->toDateTimeString(),
                    'qr_code_hash' => $ticket->qr_code_hash,
                    'pdf_url' => $ticket->pdf_url,
                    'latest_checkin' => $latestCheckin ? [
                        'scanned_at' => optional($latestCheckin->scanned_at)?->toDateTimeString(),
                        'location' => $latestCheckin->location,
                        'device' => $latestCheckin->device,
                    ] : null,
                    'download_url' => route('tickets.download', $ticket),
                    'qr_url' => route('tickets.qr', $ticket),
                ];
            })
            ->values();

        return Inertia::render('tickets/index', [
            'tickets' => $tickets,
        ]);
    }

    public function store(TicketIssueRequest $req) {
        $this->authorize('create', Ticket::class);
        $validated = $req->validated();
        $ticket = $this->svc->issue($validated['order_item_id'], $validated['user_id'] ?? null);
        return response()->json($ticket, 201);
    }

    public function show(Ticket $ticket) {
        $this->authorize('view', $ticket);
        return response()->json($ticket);
    }

    public function download(Ticket $ticket) {
        $this->authorize('view', $ticket);
        $pdf = $this->pdfs->makePdf($ticket);
        $filename = sprintf('ticket-%s.pdf', $ticket->code);

        return response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => sprintf('attachment; filename="%s"', $filename),
        ]);
    }

    public function qr(Ticket $ticket) {
        $this->authorize('view', $ticket);

        $hash = $ticket->qr_code_hash;
        $svg = $this->qr->renderSvg($hash, 8);

        $filename = sprintf('ticket-%s-qr.svg', $ticket->code);

        return response($svg, 200, [
            'Content-Type' => 'image/svg+xml; charset=UTF-8',
            'Content-Disposition' => sprintf('inline; filename="%s"', $filename),
            'Cache-Control' => 'no-store, private',
        ]);
    }

    public function transfer(TicketTransferRequest $req, Ticket $ticket) {
        $this->authorize('update', $ticket);

        $data = $req->validated();
        $toUserId = $data['to_user_id'] ?? null;

        if (!$toUserId && !empty($data['to_email'])) {
            $toUserId = User::where('email', $data['to_email'])->value('id');
        }

        $updated = $this->svc->transfer($ticket, $toUserId);

        if ($req->wantsJson()) {
            return response()->json($updated);
        }

        return back();
    }

    public function revoke(Ticket $ticket) {
        $this->authorize('update', $ticket);
        $updated = $this->svc->revoke($ticket);
        return response()->json($updated);
    }
}
