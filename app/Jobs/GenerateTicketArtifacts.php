<?php

namespace App\Jobs;

use App\Models\Ticket;
use App\Services\TicketAssetService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerateTicketArtifacts implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public int $ticketId)
    {
    }

    public function handle(TicketAssetService $assets): void
    {
        $ticket = Ticket::find($this->ticketId);

        if (!$ticket) {
            return;
        }

        $assets->generateFor($ticket);
    }
}
