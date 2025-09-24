@extends('layouts.app')
@section('title', 'EventPass - Sistema de Gestión de Eventos')

@section('content')
<div class="container-fluid">
    <!-- Hero Section -->
    <div class="row">
        <div class="col-12">
            <div class="jumbotron bg-primary text-white text-center py-5 mb-5">
                <div class="container">
                    <h1 class="display-4 font-weight-bold mb-4">EventPass</h1>
                    <p class="lead mb-4">Sistema completo de gestión de eventos y tickets</p>
                    <p class="mb-4">Crea eventos, vende tickets y gestiona todo desde un panel de administración profesional</p>
                    
                    <div class="mt-4">
        @guest
                            <a href="{{ route('register') }}" class="btn btn-light btn-lg mr-3">Registrarse</a>
                            <a href="{{ route('login') }}" class="btn btn-outline-light btn-lg">Iniciar Sesión</a>
        @endguest
        @auth
                            <a href="{{ route('dashboard') }}" class="btn btn-light btn-lg mr-3">Ir al Dashboard</a>
        @endauth
                        <a href="{{ route('events.index') }}" class="btn btn-outline-light btn-lg">Ver Eventos</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Section -->
    <div class="row mb-5">
        <div class="col-md-4 text-center">
            <div class="card h-100">
                <div class="card-body">
                    <h3 class="text-primary">{{ $events->count() }}</h3>
                    <h5 class="card-title">Eventos Activos</h5>
                    <p class="card-text">Eventos disponibles para comprar tickets</p>
                </div>
      </div>
        </div>
        <div class="col-md-4 text-center">
            <div class="card h-100">
                <div class="card-body">
                    <h3 class="text-success">{{ $events->sum('capacity') }}</h3>
                    <h5 class="card-title">Capacidad Total</h5>
                    <p class="card-text">Asientos disponibles en todos los eventos</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 text-center">
            <div class="card h-100">
                <div class="card-body">
                    <h3 class="text-info">{{ $events->min('start_time') ? $events->min('start_time')->format('M d') : 'N/A' }}</h3>
                    <h5 class="card-title">Próximo Evento</h5>
                    <p class="card-text">Fecha del próximo evento programado</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Features Section -->
    <div class="row mb-5">
        <div class="col-12">
            <h2 class="text-center mb-5">Características Principales</h2>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="fas fa-ticket-alt fa-3x text-primary mb-3"></i>
                    <h4 class="card-title">Gestión de Tickets</h4>
                    <p class="card-text">Crea y gestiona tickets para tus eventos de manera profesional</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="fas fa-qrcode fa-3x text-success mb-3"></i>
                    <h4 class="card-title">Códigos QR</h4>
                    <p class="card-text">Genera códigos QR únicos para cada ticket y facilita el check-in</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="fas fa-chart-bar fa-3x text-info mb-3"></i>
                    <h4 class="card-title">Estadísticas</h4>
                    <p class="card-text">Monitorea ventas y estadísticas en tiempo real</p>
                </div>
    </div>
    </div>
    </div>

    <!-- Events Section -->
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Eventos Disponibles</h2>
                <a href="{{ route('events.index') }}" class="btn btn-primary">Ver Todos</a>
            </div>
      </div>
    </div>

    @if($events->isEmpty())
        <div class="row">
            <div class="col-12">
                <div class="card text-center py-5">
                    <div class="card-body">
                        <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                        <h3 class="card-title">No hay eventos disponibles</h3>
                        <p class="card-text">Pronto tendremos eventos increíbles para ti</p>
                        <a href="{{ route('events.index') }}" class="btn btn-primary">Explorar Eventos</a>
                    </div>
                </div>
            </div>
      </div>
    @else
        <div class="row">
        @foreach($events as $event)
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title">{{ $event->getName() }}</h5>
                            <p class="card-text">{{ Str::limit($event->getDescription(), 100) }}</p>
                            
                            <div class="mb-3">
                                <small class="text-muted">
                                    <i class="fas fa-calendar"></i> 
                                    {{ $event->getStartTime()->format('M d, Y H:i') }}
                                </small>
                            </div>
                            
                            <div class="mb-3">
                                <small class="text-muted">
                                    <i class="fas fa-users"></i> 
                                    Capacidad: {{ $event->availableCapacity() }} / {{ $event->getCapacity() }}
                                </small>
                            </div>
                            
                            <div class="mb-3">
                                <span class="badge badge-{{ $event->getStatus() === 'published' ? 'success' : 'secondary' }}">
                                    {{ ucfirst($event->getStatus()) }}
              </span>
            </div>
                            
                            <a href="{{ route('events.show', $event) }}" class="btn btn-primary btn-sm">
                                Ver Detalles
                            </a>
              </div>
              </div>
            </div>
        @endforeach
      </div>
    @endif
</div>
@endsection
