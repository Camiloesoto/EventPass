<?php

namespace App\Http\Controllers;

use App\Http\Requests\TicketRedeemRequest;
use App\Services\TicketService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;

class TicketScanController extends Controller
{
    use AuthorizesRequests;

    public function __construct(private TicketService $svc) {}

    public function redeem(TicketRedeemRequest $req)
    {
        $this->authorize('checkin', \App\Models\Ticket::class);

        $ticket = $this->svc->redeemByHash(
            $req->string('qr_hash'),
            Auth::id(),                          // clean, no analyzer warning
            $req->header('X-Device'),
            $req->header('X-Location')
        );

        return response()->json($ticket);
    }
}
