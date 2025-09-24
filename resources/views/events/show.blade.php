@extends('layouts.app')

@section('title', $event->getName())

@section('content')
<div class="row">
    <div class="col-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Inicio</a></li>
                <li class="breadcrumb-item"><a href="{{ route('events.index') }}">Eventos</a></li>
                <li class="breadcrumb-item active">{{ $event->getName() }}</li>
            </ol>
        </nav>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title mb-0">{{ $event->getName() }}</h3>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6 class="text-muted">Fecha y Hora</h6>
                        <p class="mb-0">
                            <i class="fas fa-calendar"></i> 
                            {{ $event->getStartTime()->format('l, d F Y') }}
                        </p>
                        <p class="mb-0">
                            <i class="fas fa-clock"></i> 
                            {{ $event->getStartTime()->format('H:i') }} - {{ $event->getEndTime()->format('H:i') }}
                        </p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted">Estado</h6>
                        <span class="badge 
                            @if($event->getStatus() === 'published') badge-success
                            @elseif($event->getStatus() === 'draft') badge-warning
                            @elseif($event->getStatus() === 'cancelled') badge-danger
                            @else badge-info
                            @endif">
                            {{ ucfirst($event->getStatus()) }}
                        </span>
                    </div>
                </div>
                
                <div class="mb-4">
                    <h6 class="text-muted">Descripción</h6>
                    <p>{{ $event->getDescription() }}</p>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-muted">Capacidad Total</h6>
                        <p class="mb-0">{{ number_format($event->getCapacity()) }} asientos</p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted">Disponibilidad</h6>
                        <p class="mb-0">
                            @if($event->isSoldOut())
                                <span class="text-danger">Agotado</span>
                            @else
                                <span class="text-success">{{ $event->getAvailableCapacityText() }}</span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Acciones</h5>
            </div>
            <div class="card-body">
                @if($event->isSoldOut())
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        Este evento está agotado.
                    </div>
                    
                    @auth
                        <form method="POST" action="{{ route('events.waitlist.store', $event) }}">
                            @csrf
                            <button type="submit" class="btn btn-warning w-100">
                                <i class="fas fa-clock"></i> Unirse a Lista de Espera
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-primary w-100">
                            <i class="fas fa-sign-in-alt"></i> Iniciar Sesión para Lista de Espera
                        </a>
                    @endauth
                @else
                    @auth
                        <a href="{{ route('events.show', $event) }}#tickets" class="btn btn-primary w-100 mb-2">
                            <i class="fas fa-ticket-alt"></i> Comprar Tickets
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-primary w-100">
                            <i class="fas fa-sign-in-alt"></i> Iniciar Sesión para Comprar
                        </a>
                    @endauth
                @endif
                
                @auth
                    <div class="mt-3">
                        <a href="{{ route('events.edit', $event) }}" class="btn btn-outline-secondary w-100 mb-2">
                            <i class="fas fa-edit"></i> Editar Evento
                        </a>
                        
                        <form method="POST" action="{{ route('events.destroy', $event) }}" class="d-inline delete-form">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger w-100">
                                <i class="fas fa-trash"></i> Eliminar Evento
                            </button>
                        </form>
                    </div>
                @endauth
            </div>
        </div>
        
        @if($event->venue)
            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="card-title mb-0">Ubicación</h5>
                </div>
                <div class="card-body">
                    <h6>{{ $event->venue->name }}</h6>
                    <p class="text-muted mb-0">{{ $event->venue->address }}</p>
                </div>
            </div>
        @endif
    </div>
</div>

@if($event->ticketTypes->count() > 0)
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Tipos de Tickets</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($event->ticketTypes as $ticketType)
                            <div class="col-md-6 col-lg-4 mb-3">
                                <div class="card border">
                                    <div class="card-body">
                                        <h6 class="card-title">{{ $ticketType->name }}</h6>
                                        <p class="card-text">
                                            <strong>${{ number_format($ticketType->price, 2) }}</strong>
                                        </p>
                                        <p class="card-text">
                                            <small class="text-muted">
                                                Disponibles: {{ $ticketType->quantity }}
                                            </small>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif
@endsection