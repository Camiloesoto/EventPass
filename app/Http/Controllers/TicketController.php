<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Http\Requests\{TicketIssueRequest, TicketTransferRequest};
use App\Services\TicketService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class TicketController extends Controller
{
    use AuthorizesRequests;

    public function __construct(private TicketService $svc) {}

    public function store(TicketIssueRequest $req) {
        $this->authorize('create', Ticket::class);
        $ticket = $this->svc->issue($req->integer('user_id'), $req->validated()['meta'] ?? []);
        return response()->json($ticket, 201);
    }

    public function show(Ticket $ticket) {
        $this->authorize('view', $ticket);
        return response()->json($ticket);
    }

    public function transfer(TicketTransferRequest $req, Ticket $ticket) {
        $this->authorize('update', $ticket);
        $updated = $this->svc->transfer($ticket, $req->input('to_user_id'));
        return response()->json($updated);
    }

    public function revoke(Ticket $ticket) {
        $this->authorize('update', $ticket);
        $updated = $this->svc->revoke($ticket);
        return response()->json($updated);
    }
}