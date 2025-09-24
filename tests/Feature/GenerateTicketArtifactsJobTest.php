<?php

namespace Tests\Feature;

use App\Jobs\GenerateTicketArtifacts;
use App\Models\Ticket;
use App\Services\TicketAssetService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class GenerateTicketArtifactsJobTest extends TestCase
{
    use RefreshDatabase;

    public function test_job_creates_files_and_updates_ticket_meta(): void
    {
        Storage::fake('local');

        $ticket = Ticket::factory()->create(['meta' => null]);

        $job = new GenerateTicketArtifacts($ticket->id);
        $job->handle(app(TicketAssetService::class));

        $qrPath = 'tickets/'.$ticket->id.'/qr.txt';
        $pdfPath = 'tickets/'.$ticket->id.'/ticket.pdf';

        Storage::disk('local')->assertExists($qrPath);
        Storage::disk('local')->assertExists($pdfPath);

        $ticket->refresh();

        $this->assertEquals($qrPath, $ticket->meta['qr_code_path']);
        $this->assertEquals($pdfPath, $ticket->meta['pdf_path']);
    }
}
