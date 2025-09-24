@extends('layouts.app')

@section('title', 'Eventos')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">Eventos Disponibles</h2>
            @auth
                <a href="{{ route('events.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Crear Evento
                </a>
            @endauth
        </div>
    </div>
</div>

@if($events->count() > 0)
    <div class="row">
        @foreach($events as $event)
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card event-card h-100">
                    <div class="card-body">
                        <h5 class="card-title">
                            <a href="{{ route('events.show', $event) }}" class="text-decoration-none">
                                {{ $event->getName() }}
                            </a>
                        </h5>
                        
                        <p class="card-text text-muted">
                            {{ Str::limit($event->getDescription(), 100) }}
                        </p>
                        
                        <div class="mb-3">
                            <small class="text-muted">
                                <i class="fas fa-calendar"></i> 
                                {{ $event->getStartTime()->format('d/m/Y H:i') }}
                            </small>
                        </div>
                        
                        <div class="mb-3">
                            <span class="badge 
                                @if($event->isSoldOut()) badge-danger
                                @elseif($event->availableCapacity() < 10) badge-warning
                                @else badge-success
                                @endif">
                                @if($event->isSoldOut())
                                    Agotado
                                @else
                                    {{ $event->getAvailableCapacityText() }}
                                @endif
                            </span>
                            
                            <span class="badge badge-info ms-2">
                                {{ ucfirst($event->getStatus()) }}
                            </span>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <a href="{{ route('events.show', $event) }}" class="btn btn-outline-primary">
                                Ver Detalles
                            </a>
                            
                            @if($event->isSoldOut())
                                <form method="POST" action="{{ route('events.waitlist.store', $event) }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-warning w-100">
                                        <i class="fas fa-clock"></i> Lista de Espera
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    
    <!-- Pagination -->
    <div class="d-flex justify-content-center">
        {{ $events->links() }}
    </div>
@else
    <div class="text-center py-5">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">No hay eventos disponibles</h5>
                <p class="card-text text-muted">
                    No se encontraron eventos en este momento. Vuelve más tarde.
                </p>
                @auth
                    <a href="{{ route('events.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Crear Primer Evento
                    </a>
                @endauth
            </div>
        </div>
    </div>
@endif
@endsection