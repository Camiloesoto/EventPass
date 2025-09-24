<?php

namespace Tests\Feature;

use App\Enums\TicketStatus;
use App\Models\{Ticket, TicketCheckin, User};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class UserTicketsTest extends TestCase
{
    use RefreshDatabase;

    public function test_tickets_page_requires_authentication(): void
    {
        $this->get(route('tickets.index'))->assertRedirect(route('login'));
    }

    public function test_user_can_view_their_tickets(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        try {
            Carbon::setTestNow(Carbon::parse('2024-03-01 12:00:00'));
            $firstTicket = Ticket::factory()->for($user)->create([
                'meta' => ['seat' => 'B2'],
            ]);

            Carbon::setTestNow(Carbon::parse('2024-03-05 18:30:00'));
            $secondTicket = Ticket::factory()->for($user)->create([
                'status' => TicketStatus::redeemed,
                'redeemed_at' => Carbon::now(),
                'meta' => ['seat' => 'C5'],
            ]);
        } finally {
            Carbon::setTestNow();
        }

        $otherTicket = Ticket::factory()->for($otherUser)->create();

        $scanTime = Carbon::parse('2024-03-06 09:15:00');

        TicketCheckin::create([
            'ticket_id' => $secondTicket->id,
            'scanned_by_user_id' => $otherUser->id,
            'scanned_at' => $scanTime,
            'location' => 'North entrance',
            'device' => 'Handheld scanner',
        ]);

        $response = $this->actingAs($user)->get(route('tickets.index'));

        $response->assertOk();

        $response->assertInertia(fn (Assert $page) => $page
            ->component('tickets/index')
            ->has('tickets', 2)
            ->where('tickets', fn ($items) => collect($items)->pluck('id')->all() === [
                $secondTicket->id,
                $firstTicket->id,
            ])
            ->where('tickets.0.latest_checkin.location', 'North entrance')
            ->where('tickets.0.latest_checkin.device', 'Handheld scanner')
            ->where('tickets.0.latest_checkin.scanned_at', $scanTime->toDateTimeString())
            ->where('tickets.0.download_url', route('tickets.download', $secondTicket))
            ->where('tickets.0.qr_url', route('tickets.qr', $secondTicket))
            ->where('tickets.1.latest_checkin', null)
            ->where('tickets.1.download_url', route('tickets.download', $firstTicket))
            ->where('tickets.1.qr_url', route('tickets.qr', $firstTicket))
        );

        $this->assertDatabaseMissing('tickets', [
            'id' => $otherTicket->id,
            'user_id' => $user->id,
        ]);
    }
}
