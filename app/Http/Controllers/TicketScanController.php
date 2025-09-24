<?php

namespace App\Http\Controllers;

use App\Http\Requests\TicketRedeemRequest;
use App\Models\Ticket;
use App\Services\TicketService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class TicketScanController extends Controller
{
    use AuthorizesRequests;

    public function __construct(private TicketService $svc) {}

    public function screen(): Response
    {
        $this->authorize('checkin', Ticket::class);

        return Inertia::render('tickets/admin-scan', [
            'redeemRoute' => route('tickets.redeem'),
        ]);
    }

    public function redeem(TicketRedeemRequest $req)
    {
        $this->authorize('checkin', Ticket::class);

        $ticket = $this->svc->redeemByIdentifier(
            $req->string('qr_code_hash'),
            Auth::id(),                          // clean, no analyzer warning
            $req->header('X-Device'),
            $req->header('X-Location')
        );

        return response()->json($ticket);
    }
}
