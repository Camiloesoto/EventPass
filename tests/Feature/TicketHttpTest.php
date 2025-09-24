<?php

namespace Tests\Feature;

use App\Enums\TicketStatus;
use App\Models\{Ticket, User};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketHttpTest extends TestCase
{
    use RefreshDatabase;

    public function test_issue_endpoint_requires_auth(): void
    {
        $this->postJson('/tickets', ['user_id' => null, 'meta' => []])->assertStatus(401);
    }

    public function test_issue_transfer_redeem_revoke_happy_path(): void
    {
        $owner = User::factory()->create();
        $staff = User::factory()->create();
        $target = User::factory()->create();

        $response = $this->actingAs($owner)
            ->postJson('/tickets', ['user_id' => $owner->id, 'meta' => ['seat' => 'C3']])
            ->assertCreated()
            ->json();
        $ticketId = $response['id'];

        $this->actingAs($owner)->getJson("/tickets/{$ticketId}")
            ->assertOk()->assertJsonPath('id', $ticketId);

        $this->actingAs($owner)->postJson("/tickets/{$ticketId}/transfer", ['to_user_id' => $target->id])
            ->assertOk()->assertJsonPath('user_id', $target->id);

        $hash = Ticket::find($ticketId)->qr_code_hash;
        $this->actingAs($staff)
            ->postJson('/tickets/redeem', ['qr_code_hash' => $hash], ['X-Device' => 'scanner1', 'X-Location' => 'gate A'])
            ->assertOk()->assertJsonPath('status', TicketStatus::redeemed->value);

        $this->actingAs($owner)->postJson("/tickets/{$ticketId}/revoke")->assertForbidden();
    }

    public function test_owner_can_revoke_before_redeem(): void
    {
        $owner = User::factory()->create();
        $ticket = Ticket::factory()->for($owner, 'owner')->create();
        $ticket->user_id = $owner->id;
        $ticket->save();

        $this->actingAs($owner)->postJson("/tickets/{$ticket->id}/revoke")
            ->assertOk()->assertJsonPath('status', TicketStatus::cancelled->value);
    }

    public function test_transfer_by_email_assigns_new_owner(): void
    {
        $owner = User::factory()->create();
        $recipient = User::factory()->create();
        $ticket = Ticket::factory()->for($owner, 'owner')->create();
        $ticket->user_id = $owner->id;
        $ticket->save();

        $this->actingAs($owner)
            ->postJson("/tickets/{$ticket->id}/transfer", ['to_email' => strtoupper($recipient->email)])
            ->assertOk()
            ->assertJsonPath('user_id', $recipient->id)
            ->assertJsonPath('status', TicketStatus::transferred->value);
    }

    public function test_staff_can_redeem_using_ticket_code(): void
    {
        $staff = User::factory()->create();
        $ticket = Ticket::factory()->create();

        $this->actingAs($staff)
            ->postJson('/tickets/redeem', ['qr_code_hash' => strtolower($ticket->code)])
            ->assertOk()
            ->assertJsonPath('status', TicketStatus::redeemed->value);
    }
}
