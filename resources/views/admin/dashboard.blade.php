@extends('layouts.admin')

@section('title', 'Dashboard - Panel de Administración')
@section('page-title', 'Dashboard')

@section('content')
<div class="row">
    <!-- Estadísticas principales -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Total Eventos
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_events'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-calendar fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Eventos Activos
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['active_events'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-play-circle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            Total Usuarios
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_users'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Tickets Vendidos
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_tickets_sold'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-ticket-alt fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Ingresos -->
    <div class="col-xl-8 col-lg-7">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Ingresos Mensuales</h6>
            </div>
            <div class="card-body">
                <div class="chart-area">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Eventos Populares -->
    <div class="col-xl-4 col-lg-5">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Eventos Más Populares</h6>
            </div>
            <div class="card-body">
                @forelse($popular_events as $event)
                    <div class="d-flex align-items-center mb-3">
                        <div class="flex-grow-1">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                {{ $event->name }}
                            </div>
                            <div class="text-xs text-gray-600">
                                {{ $event->ticket_types_count }} tipos de tickets
                            </div>
                        </div>
                        <div class="text-xs text-gray-500">
                            {{ $event->getStartTime()->format('M d') }}
                        </div>
                    </div>
                @empty
                    <p class="text-muted">No hay eventos disponibles.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Eventos Recientes -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Eventos Recientes</h6>
            </div>
            <div class="card-body">
                @forelse($recent_events as $event)
                    <div class="d-flex align-items-center mb-3">
                        <div class="flex-grow-1">
                            <div class="text-sm font-weight-bold text-gray-800">
                                <a href="{{ route('admin.events.show', $event) }}">{{ $event->name }}</a>
                            </div>
                            <div class="text-xs text-gray-600">
                                {{ $event->venue?->name ?? 'Sin venue' }} • {{ $event->getStartTime()->format('M d, Y') }}
                            </div>
                        </div>
                        <span class="badge badge-{{ $event->status === 'active' ? 'success' : 'secondary' }}">
                            {{ ucfirst($event->status) }}
                        </span>
                    </div>
                @empty
                    <p class="text-muted">No hay eventos recientes.</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Usuarios Recientes -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Usuarios Recientes</h6>
            </div>
            <div class="card-body">
                @forelse($recent_users as $user)
                    <div class="d-flex align-items-center mb-3">
                        <div class="flex-grow-1">
                            <div class="text-sm font-weight-bold text-gray-800">
                                <a href="{{ route('admin.users.show', $user) }}">{{ $user->name }}</a>
                            </div>
                            <div class="text-xs text-gray-600">{{ $user->email }}</div>
                        </div>
                        <div class="text-xs text-gray-500">
                            {{ $user->created_at->format('M d') }}
                        </div>
                    </div>
                @empty
                    <p class="text-muted">No hay usuarios recientes.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Gráfico de ingresos
const ctx = document.getElementById('revenueChart').getContext('2d');
const revenueChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: {!! json_encode(array_column($monthly_stats, 'month')) !!},
        datasets: [{
            label: 'Ingresos ($)',
            data: {!! json_encode(array_column($monthly_stats, 'revenue')) !!},
            borderColor: 'rgb(75, 192, 192)',
            backgroundColor: 'rgba(75, 192, 192, 0.2)',
            tension: 0.1
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});
</script>

<style>
.border-left-primary {
    border-left: 0.25rem solid #4e73df !important;
}
.border-left-success {
    border-left: 0.25rem solid #1cc88a !important;
}
.border-left-info {
    border-left: 0.25rem solid #36b9cc !important;
}
.border-left-warning {
    border-left: 0.25rem solid #f6c23e !important;
}
.text-primary {
    color: #4e73df !important;
}
.text-success {
    color: #1cc88a !important;
}
.text-info {
    color: #36b9cc !important;
}
.text-warning {
    color: #f6c23e !important;
}
.badge-success {
    background-color: #1cc88a;
}
.badge-secondary {
    background-color: #858796;
}
</style>
@endsection
