<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\User;
use App\Models\Ticket;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Estadísticas para el dashboard
        $stats = [
            'total_events' => Event::count(),
            'active_events' => Event::where('status', 'published')->count(),
            'total_users' => User::count(),
            'total_tickets_sold' => Ticket::count(),
        ];
        
        // Eventos populares (con más tickets vendidos)
        $popular_events = Event::withCount('ticketTypes')
            ->orderBy('ticket_types_count', 'desc')
            ->limit(5)
            ->get();
        
        // Eventos recientes
        $recent_events = Event::with('venue')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        // Mis tickets (si es usuario normal)
        $my_tickets = [];
        if ($user && !$user->is_admin) {
            $my_tickets = Ticket::where('user_id', $user->id)
                ->with(['orderItem.ticketType.event'])
                ->limit(5)
                ->get();
        }
        
        return view('dashboard', compact('stats', 'popular_events', 'recent_events', 'my_tickets'));
    }
}