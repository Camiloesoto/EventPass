<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Ticket;

class TicketPolicy
{
    public function view(User $user, Ticket $ticket): bool {
        return $user->id === $ticket->user_id; // or hasRole('organizer')
    }
    public function update(User $user, Ticket $ticket): bool {
        return $user->id === $ticket->user_id;
    }
    public function create(User $user): bool {
        return true; }
    public function checkin(User $user): bool {
        return true; // tighten for staff later
    }
}
