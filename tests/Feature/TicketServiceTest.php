<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Services\TicketService;
use App\Models\{Ticket, User, TicketCheckin};
use DomainException;

class TicketServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_issue_generates_identifiers(): void
    {
        $u = User::factory()->create();
        $t = app(TicketService::class)->issue($u->id, ['seat'=>'B2']);
        $this->assertNotNull($t->code);
        $this->assertNotNull($t->qr_hash);
        $this->assertEquals(Ticket::STATUS_ISSUED, $t->status);
        $this->assertDatabaseHas('tickets', ['id'=>$t->id,'status'=>Ticket::STATUS_ISSUED]);
    }

    public function test_transfer_updates_owner_and_status(): void
    {
        $t = Ticket::factory()->create();
        $to = User::factory()->create();
        $t = app(TicketService::class)->transfer($t, $to->id);
        $this->assertEquals($to->id, $t->user_id);
        $this->assertEquals(Ticket::STATUS_TRANSFERRED, $t->status);
    }

    public function test_redeem_creates_checkin_and_is_idempotent(): void
    {
        $scanner = User::factory()->create();
        $t = Ticket::factory()->create();

        $first = app(TicketService::class)->redeemByHash($t->qr_hash, $scanner->id, 'ios', 'gate A');
        $this->assertEquals(Ticket::STATUS_REDEEMED, $first->status);
        $this->assertNotNull($first->redeemed_at);
        $this->assertDatabaseCount('ticket_checkins', 1);

        $second = app(TicketService::class)->redeemByHash($t->qr_hash, $scanner->id, 'ios', 'gate A');
        $this->assertEquals($first->id, $second->id);
        $this->assertDatabaseCount('ticket_checkins', 1); // still one
    }

    public function test_revoke_blocks_redeem(): void
    {
        $t = Ticket::factory()->create();
        $t = app(TicketService::class)->revoke($t, 'duplicate');

        $this->assertEquals(Ticket::STATUS_CANCELLED, $t->status);
        $this->expectException(DomainException::class);
        app(TicketService::class)->redeemByHash($t->qr_hash, null, null, null);
    }
}
