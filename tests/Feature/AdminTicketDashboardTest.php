<?php

namespace Tests\Feature;

use App\Enums\TicketStatus;
use App\Models\{Ticket, TicketCheckin, User};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class AdminTicketDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_ticket_dashboard_requires_authentication(): void
    {
        $this->get('/admin/tickets')->assertRedirect('/login');
    }

    public function test_ticket_scan_page_requires_authentication(): void
    {
        $this->get('/admin/tickets/scan')->assertRedirect('/login');
    }

    public function test_ticket_scan_page_renders_inertia_view(): void
    {
        $user = User::factory()->create(['email_verified_at' => now()]);

        $response = $this->actingAs($user)->get('/admin/tickets/scan');

        $response
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('tickets/admin-scan')
                ->where('redeemRoute', route('tickets.redeem'))
            );
    }

    public function test_admin_ticket_dashboard_displays_ticket_summary(): void
    {
        $admin = User::factory()->create();

        Ticket::factory()->count(2)->create([
            'status' => TicketStatus::issued,
        ]);

        Ticket::factory()->create([
            'status' => TicketStatus::transferred,
        ]);

        $redeemedTickets = Ticket::factory()->count(3)->create([
            'status' => TicketStatus::redeemed,
            'redeemed_at' => now(),
        ]);

        Ticket::factory()->create([
            'status' => TicketStatus::cancelled,
            'revoked_at' => now(),
        ]);

        $checkinTicket = $redeemedTickets->first();

        TicketCheckin::create([
            'ticket_id' => $checkinTicket->id,
            'scanned_by_user_id' => $admin->id,
            'scanned_at' => now(),
            'device' => 'Gate scanner',
            'location' => 'Main entrance',
        ]);

        $response = $this->actingAs($admin)->get('/admin/tickets');

        $response->assertOk();

        $response->assertInertia(fn (Assert $page) => $page
            ->component('tickets/admin-dashboard')
            ->has('stats', fn (Assert $stats) => $stats
                ->where('total', 7)
                ->where('active', 6)
                ->where('redeemed', 3)
                ->where('cancelled', 1)
                ->etc()
            )
            ->has('statusBreakdown', 4)
            ->has('statusBreakdown.0', fn (Assert $status) => $status
                ->where('status', TicketStatus::issued->value)
                ->where('count', 2)
                ->etc()
            )
            ->has('tickets.0', fn (Assert $ticket) => $ticket
                ->hasAll(['id', 'code', 'status', 'issued_at', 'redeemed_at', 'meta'])
                ->has('owner', fn (Assert $owner) => $owner
                    ->hasAll(['id', 'name', 'email'])
                )
            )
            ->has('users.0', fn (Assert $user) => $user
                ->hasAll(['id', 'name', 'email'])
            )
            ->where('statusOptions', [
                TicketStatus::issued->value,
                TicketStatus::transferred->value,
                TicketStatus::redeemed->value,
                TicketStatus::cancelled->value,
            ])
            ->has('recentTickets.0', fn (Assert $ticket) => $ticket
                ->hasAll(['id', 'code', 'status', 'owner_name', 'issued_at', 'redeemed_at'])
            )
            ->has('recentCheckins.0', fn (Assert $checkin) => $checkin
                ->where('ticket_code', $checkinTicket->code)
                ->where('scanner_name', $admin->name)
                ->etc()
            )
        );
    }

    public function test_admin_can_create_ticket_from_dashboard(): void
    {
        $admin = User::factory()->create();
        $owner = User::factory()->create();

        Carbon::setTestNow(Carbon::now());

        try {
            $response = $this->actingAs($admin)->post('/admin/tickets', [
                'owner_email' => $owner->email,
                'status' => TicketStatus::transferred->value,
                'meta' => ['seat' => 'B12'],
            ]);

            $response->assertRedirect(route('tickets.admin'));

            $this->assertDatabaseCount('tickets', 1);

            $ticket = Ticket::first();

            $this->assertNotNull($ticket);
            $this->assertSame($owner->id, $ticket->user_id);
            $this->assertTrue($ticket->status === TicketStatus::transferred);
            $this->assertSame(['seat' => 'B12'], $ticket->meta);
            $this->assertNull($ticket->redeemed_at);
            $this->assertNull($ticket->revoked_at);
        } finally {
            Carbon::setTestNow();
        }
    }

    public function test_admin_can_update_ticket_from_dashboard(): void
    {
        $admin = User::factory()->create();
        $newOwner = User::factory()->create();
        $ticket = Ticket::factory()->create([
            'status' => TicketStatus::issued,
            'redeemed_at' => null,
            'revoked_at' => null,
        ]);

        Carbon::setTestNow($now = Carbon::now()->addHour());

        try {
            $response = $this->actingAs($admin)->put("/admin/tickets/{$ticket->id}", [
                'owner_email' => $newOwner->email,
                'status' => TicketStatus::redeemed->value,
                'meta' => ['note' => 'VIP'],
            ]);

            $response->assertRedirect(route('tickets.admin'));

            $ticket->refresh();

            $this->assertSame($newOwner->id, $ticket->user_id);
            $this->assertTrue($ticket->status === TicketStatus::redeemed);
            $this->assertSame(['note' => 'VIP'], $ticket->meta);
            $this->assertNotNull($ticket->redeemed_at);
            $this->assertTrue($ticket->redeemed_at->equalTo($now));
            $this->assertNull($ticket->revoked_at);
        } finally {
            Carbon::setTestNow();
        }
    }

    public function test_admin_can_cancel_ticket_from_dashboard(): void
    {
        $admin = User::factory()->create();
        $ticket = Ticket::factory()->create([
            'status' => TicketStatus::redeemed,
            'redeemed_at' => Carbon::now()->subDay(),
        ]);

        Carbon::setTestNow($now = Carbon::now());

        try {
            $response = $this->actingAs($admin)->put("/admin/tickets/{$ticket->id}", [
                'owner_email' => $ticket->owner?->email,
                'status' => TicketStatus::cancelled->value,
                'meta' => ['reason' => 'Duplicate'],
            ]);

            $response->assertRedirect(route('tickets.admin'));

            $ticket->refresh();

            $this->assertTrue($ticket->status === TicketStatus::cancelled);
            $this->assertNull($ticket->redeemed_at);
            $this->assertNotNull($ticket->revoked_at);
            $this->assertTrue($ticket->revoked_at->equalTo($now));
        } finally {
            Carbon::setTestNow();
        }
    }

    public function test_admin_can_delete_ticket_from_dashboard(): void
    {
        $admin = User::factory()->create();
        $ticket = Ticket::factory()->create();

        $response = $this->actingAs($admin)->delete("/admin/tickets/{$ticket->id}");

        $response->assertRedirect(route('tickets.admin'));

        $this->assertDatabaseMissing('tickets', ['id' => $ticket->id]);
    }
}
