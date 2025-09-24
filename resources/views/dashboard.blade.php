@extends('layouts.app')

@section('title', 'Dashboard - EventPass')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <h1 class="mb-4">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </h1>
        </div>
    </div>

    <!-- Estadísticas principales -->
    <div class="row mb-4">
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
        <!-- Eventos Populares -->
        <div class="col-xl-6 col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Eventos Más Populares</h6>
                </div>
                <div class="card-body">
                    @forelse($popular_events as $event)
                        <div class="d-flex align-items-center mb-3">
                            <div class="flex-grow-1">
                                <div class="text-sm font-weight-bold text-gray-800">
                                    <a href="{{ route('events.show', $event) }}">{{ $event->getName() }}</a>
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

        <!-- Eventos Recientes -->
        <div class="col-xl-6 col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Eventos Recientes</h6>
                </div>
                <div class="card-body">
                    @forelse($recent_events as $event)
                        <div class="d-flex align-items-center mb-3">
                            <div class="flex-grow-1">
                                <div class="text-sm font-weight-bold text-gray-800">
                                    <a href="{{ route('events.show', $event) }}">{{ $event->getName() }}</a>
                                </div>
                                <div class="text-xs text-gray-600">
                                    {{ $event->venue?->getName() ?? 'Sin venue' }} • {{ $event->getStartTime()->format('M d, Y') }}
                                </div>
                            </div>
                            <span class="badge badge-{{ $event->getStatus() === 'published' ? 'success' : 'secondary' }}">
                                {{ ucfirst($event->getStatus()) }}
                            </span>
                        </div>
                    @empty
                        <p class="text-muted">No hay eventos recientes.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    @if(Auth::user() && !Auth::user()->is_admin && $my_tickets->count() > 0)
    <div class="row">
        <!-- Mis Tickets -->
        <div class="col-12 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Mis Tickets Recientes</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($my_tickets as $ticket)
                            <div class="col-md-6 col-lg-4 mb-3">
                                <div class="card border-left-info">
                                    <div class="card-body">
                                        <div class="text-sm font-weight-bold text-gray-800">
                                            {{ $ticket->orderItem->ticketType->event->getName() }}
                                        </div>
                                        <div class="text-xs text-gray-600">
                                            Ticket #{{ $ticket->code }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            {{ $ticket->getCreatedAt()->format('M d, Y') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="text-center mt-3">
                        <a href="{{ route('tickets.index') }}" class="btn btn-primary btn-sm">
                            Ver Todos Mis Tickets
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Acciones rápidas -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Acciones Rápidas</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('events.index') }}" class="btn btn-outline-primary btn-block">
                                <i class="fas fa-calendar"></i> Ver Eventos
                            </a>
                        </div>
                        @if(Auth::user() && !Auth::user()->is_admin)
                            <div class="col-md-3 mb-3">
                                <a href="{{ route('tickets.index') }}" class="btn btn-outline-success btn-block">
                                    <i class="fas fa-ticket-alt"></i> Mis Tickets
                                </a>
                            </div>
                        @endif
                        @if(Auth::user() && Auth::user()->is_admin)
                            <div class="col-md-3 mb-3">
                                <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-warning btn-block">
                                    <i class="fas fa-cogs"></i> Panel Admin
                                </a>
                            </div>
                        @endif
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('home') }}" class="btn btn-outline-info btn-block">
                                <i class="fas fa-home"></i> Página Principal
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
