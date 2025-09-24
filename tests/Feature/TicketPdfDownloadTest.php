<?php

namespace Tests\Feature;

use App\Models\{Ticket, User};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketPdfDownloadTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_download_ticket_pdf(): void
    {
        $user = User::factory()->create();
        $ticket = Ticket::factory()->for($user, 'owner')->create([
            'meta' => [
                'seat' => 'B12',
                'event' => 'Unit Test Live',
            ],
        ]);

        $response = $this->actingAs($user)->get("/tickets/{$ticket->id}/download");

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
        $response->assertHeader('content-disposition', 'attachment; filename="ticket-' . $ticket->code . '.pdf"');

        $pdf = $response->getContent();
        $this->assertIsString($pdf);
        $this->assertStringStartsWith('%PDF', $pdf);
        $this->assertGreaterThan(1000, strlen($pdf));
    }
}

