<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\{User, Ticket};

class TicketHttpTest extends TestCase
{
    use RefreshDatabase;

    public function test_issue_endpoint_requires_auth(): void
    {
        $this->postJson('/tickets', ['user_id'=>null,'meta'=>[]])->assertStatus(401);
    }

    public function test_issue_transfer_redeem_revoke_happy_path(): void
    {
        $owner  = User::factory()->create();
        $staff  = User::factory()->create();
        $target = User::factory()->create();

        // Issue
        $res = $this->actingAs($owner)
            ->postJson('/tickets', ['user_id'=>$owner->id,'meta'=>['seat'=>'C3']])
            ->assertCreated()
            ->json();
        $ticketId = $res['id'];

        // Show
        $this->actingAs($owner)->getJson("/tickets/{$ticketId}")
            ->assertOk()->assertJsonPath('id', $ticketId);

        // Transfer
        $this->actingAs($owner)->postJson("/tickets/{$ticketId}/transfer", ['to_user_id'=>$target->id])
            ->assertOk()->assertJsonPath('user_id', $target->id);

        // Redeem (staff)
        $qr = Ticket::find($ticketId)->qr_hash;
        $this->actingAs($staff)
            ->postJson('/tickets/redeem', ['qr_hash'=>$qr], ['X-Device'=>'scanner1','X-Location'=>'gate A'])
            ->assertOk()->assertJsonPath('status', Ticket::STATUS_REDEEMED);

        // Revoke should now fail due to policy owner mismatch
        $this->actingAs($owner)->postJson("/tickets/{$ticketId}/revoke")->assertForbidden();
    }

    public function test_owner_can_revoke_before_redeem(): void
    {
        $owner = User::factory()->create();
        $ticket = Ticket::factory()->for($owner, 'owner')->create(); // uses owner() relation alias if defined; else set user_id below
        $ticket->user_id = $owner->id; $ticket->save();

        $this->actingAs($owner)->postJson("/tickets/{$ticket->id}/revoke")
            ->assertOk()->assertJsonPath('status', Ticket::STATUS_CANCELLED);
    }
}
