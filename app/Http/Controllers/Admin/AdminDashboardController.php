<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\User;
use App\Models\Order;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function index()
    {
        // Log de acceso al dashboard
        Log::info('Admin accedió al dashboard', [
            'admin_id' => auth()->id(),
            'admin_email' => auth()->user()->email
        ]);

        try {
            // Estadísticas generales
            $stats = [
                'total_events' => Event::count(),
                'active_events' => Event::where('status', 'active')->count(),
                'total_users' => User::count(),
                'total_orders' => Order::count(),
                'total_tickets_sold' => Ticket::count(),
                'total_revenue' => Order::where('status', 'completed')->sum('total_amount'),
            ];

            // Eventos recientes
            $recent_events = Event::with(['venue'])
                ->latest('created_at')
                ->limit(5)
                ->get();

            // Usuarios recientes
            $recent_users = User::latest('created_at')
                ->limit(5)
                ->get();

            // Órdenes recientes
            $recent_orders = Order::with(['user', 'items.ticketType.event'])
                ->latest('created_at')
                ->limit(5)
                ->get();

            // Eventos más populares (por tickets vendidos)
            $popular_events = Event::withCount('ticketTypes')
                ->orderBy('ticket_types_count', 'desc')
                ->limit(5)
                ->get();

            // Estadísticas por mes (últimos 6 meses)
            $monthly_stats = $this->getMonthlyStats();

            return view('admin.dashboard', compact(
                'stats',
                'recent_events',
                'recent_users',
                'recent_orders',
                'popular_events',
                'monthly_stats'
            ));

        } catch (\Exception $e) {
            Log::error('Error al cargar dashboard de admin', [
                'admin_id' => auth()->id(),
                'admin_email' => auth()->user()->email,
                'error' => $e->getMessage()
            ]);

            return view('admin.dashboard')->with('error', 'Error al cargar las estadísticas.');
        }
    }

    /**
     * Get monthly statistics for the last 6 months.
     */
    private function getMonthlyStats()
    {
        $months = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $month = $date->format('Y-m');
            
            $months[] = [
                'month' => $date->format('M Y'),
                'events' => Event::whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count(),
                'users' => User::whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count(),
                'orders' => Order::whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count(),
                'revenue' => Order::whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->where('status', 'completed')
                    ->sum('total_amount'),
            ];
        }

        return $months;
    }
}