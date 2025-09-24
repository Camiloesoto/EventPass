<?php

namespace Tests\Feature;

use App\Enums\TicketStatus;
use App\Jobs\GenerateTicketArtifacts;
use App\Models\{Ticket, TicketCheckin, User};
use App\Services\TicketService;
use DomainException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Tests\TestCase;

class TicketServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_issue_generates_identifiers(): void
    {
        $user = User::factory()->create();
        $ticket = app(TicketService::class)->issue($user->id, ['seat' => 'B2']);

        $this->assertNotNull($ticket->code);
        $this->assertNotNull($ticket->qr_code_hash);
        $this->assertTrue($ticket->status === TicketStatus::issued);
        $this->assertDatabaseHas('tickets', ['id' => $ticket->id, 'status' => TicketStatus::issued->value]);
    }

    public function test_issue_dispatches_artifact_generation_job(): void
    {
        Bus::fake();

        $user = User::factory()->create();
        $ticket = app(TicketService::class)->issue($user->id, []);

        Bus::assertDispatched(GenerateTicketArtifacts::class, function ($job) use ($ticket) {
            return $job->ticketId === $ticket->id;
        });
    }

    public function test_transfer_updates_owner_and_status(): void
    {
        $ticket = Ticket::factory()->create();
        $recipient = User::factory()->create();

        $ticket = app(TicketService::class)->transfer($ticket, $recipient->id);

        $this->assertEquals($recipient->id, $ticket->user_id);
        $this->assertTrue($ticket->status === TicketStatus::transferred);
    }

    public function test_redeem_creates_checkin_and_is_idempotent(): void
    {
        $scanner = User::factory()->create();
        $ticket = Ticket::factory()->create();

        $first = app(TicketService::class)->redeemByHash($ticket->qr_code_hash, $scanner->id, 'ios', 'gate A');
        $this->assertTrue($first->status === TicketStatus::redeemed);
        $this->assertNotNull($first->redeemed_at);
        $this->assertDatabaseCount('ticket_checkins', 1);

        $second = app(TicketService::class)->redeemByHash($ticket->qr_code_hash, $scanner->id, 'ios', 'gate A');
        $this->assertEquals($first->id, $second->id);
        $this->assertDatabaseCount('ticket_checkins', 1);
    }

    public function test_redeem_accepts_ticket_code(): void
    {
        $scanner = User::factory()->create();
        $ticket = Ticket::factory()->create();

        $result = app(TicketService::class)->redeemByIdentifier(strtolower($ticket->code), $scanner->id, 'android', 'gate B');

        $this->assertTrue($result->status === TicketStatus::redeemed);
        $this->assertNotNull($result->redeemed_at);
        $this->assertDatabaseCount('ticket_checkins', 1);
    }

    public function test_revoke_blocks_redeem(): void
    {
        $ticket = Ticket::factory()->create();
        $ticket = app(TicketService::class)->revoke($ticket, 'duplicate');

        $this->assertTrue($ticket->status === TicketStatus::cancelled);
        $this->expectException(DomainException::class);

        app(TicketService::class)->redeemByHash($ticket->qr_code_hash, null, null, null);
    }
}
