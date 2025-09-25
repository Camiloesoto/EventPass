<?php

namespace App\Http\Controllers;

use App\Services\TicketPurchaseService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class TicketController extends Controller
{
    private TicketPurchaseService $ticketPurchaseService;

    public function __construct(TicketPurchaseService $ticketPurchaseService)
    {
        $this->ticketPurchaseService = $ticketPurchaseService;
    }
    public function index(): View
    {
        $tickets = Auth::user()->tickets()
            ->with(['orderItem.ticketType.event'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('tickets.index', [
            'tickets' => $tickets
        ]);
    }

    public function purchase(Request $request, int $eventId): RedirectResponse
    {
        $request->validate([
            'ticket_types' => 'required|array|min:1',
            'ticket_types.*.id' => 'required|exists:ticket_types,id',
            'ticket_types.*.quantity' => 'required|integer|min:0|max:10',
        ]);

        $hasValidQuantity = $this->validateTicketQuantities($request->ticket_types);
        
        if (!$hasValidQuantity) {
            return back()->withErrors(['ticket_types' => __('events.select_tickets')]);
        }

        try {
            $order = $this->ticketPurchaseService->purchaseTickets($request->ticket_types, $eventId);
            
            return redirect()->route('tickets.index')
                ->with('success', __('events.purchase_successful'));

        } catch (\Exception $e) {
            return back()->with('error', __('events.purchase_failed', ['error' => $e->getMessage()]));
        }
    }

    public function showQr(int $id): View
    {
        $ticket = Ticket::with(['orderItem.ticketType.event'])
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        return view('tickets.qr', [
            'ticket' => $ticket
        ]);
    }

    public function download(int $id): Response
    {
        $ticket = Ticket::with(['orderItem.ticketType.event'])
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        $content = $this->generateTicketContent($ticket);

        return response($content)
            ->header('Content-Type', 'text/plain')
            ->header('Content-Disposition', 'attachment; filename="ticket-' . $ticket->getCode() . '.txt"');
    }

    public function transfer(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        try {
            $ticket = $this->ticketPurchaseService->transferTicket($id, $request->email);

            return redirect()->route('tickets.index')
                ->with('success', __('events.transfer_successful', ['name' => $ticket->user->getName()]));

        } catch (\Exception $e) {
            return back()->withErrors(['email' => __('events.transfer_failed', ['error' => $e->getMessage()])]);
        }
    }

    private function validateTicketQuantities(array $ticketTypes): bool
    {
        foreach ($ticketTypes as $ticketTypeData) {
            if ((int) $ticketTypeData['quantity'] > 0) {
                return true;
            }
        }
        return false;
    }

    private function generateTicketContent(Ticket $ticket): string
    {
        return "Ticket: {$ticket->getCode()}\n" .
               "Event: {$ticket->orderItem->ticketType->event->getName()}\n" .
               "Type: {$ticket->orderItem->ticketType->getName()}\n" .
               "Price: $" . number_format($ticket->orderItem->getUnitPrice(), 2) . "\n" .
               "Status: {$ticket->getStatus()->value}\n" .
               "QR Code: {$ticket->getQrCodeHash()}\n";
    }
}