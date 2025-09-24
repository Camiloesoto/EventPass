@extends('layouts.app')

@section('title', 'Events - EventPass')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="mb-0">
                    <i class="fas fa-calendar-alt"></i> Events
                </h1>
                @auth
                    @if(auth()->user()->is_admin)
                        <a href="{{ route('events.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Create Event
                        </a>
                    @endif
                @endauth
            </div>
        </div>
    </div>

    @if($events->count() > 0)
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
                                    <i class="fas fa-map-marker-alt"></i> 
                                    {{ $event->venue?->getName() ?? 'TBD' }}
                                </small>
                            </div>
                            
                            <div class="mb-3">
                                <small class="text-muted">
                                    <i class="fas fa-users"></i> 
                                    Capacity: {{ $event->getCapacity() }}
                                </small>
                            </div>
                            
                            <div class="mb-3">
                                <span class="badge badge-{{ $event->getStatus()->value === 'published' ? 'success' : 'secondary' }}">
                                    {{ ucfirst($event->getStatus()->value) }}
                                </span>
                            </div>
                            
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('events.show', $event) }}" class="btn btn-primary btn-sm">
                                    View Details
                                </a>
                                
                                @auth
                                    @if(auth()->user()->is_admin)
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('events.edit', $event) }}" class="btn btn-outline-secondary btn-sm">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form method="POST" action="{{ route('events.destroy', $event) }}" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger btn-sm" 
                                                        onclick="return confirm('Are you sure you want to delete this event?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    @endif
                                @endauth
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="row">
            <div class="col-12">
                {{ $events->links() }}
            </div>
        </div>
    @else
        <div class="row">
            <div class="col-12">
                <div class="card text-center py-5">
                    <div class="card-body">
                        <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                        <h3 class="card-title">No Events Available</h3>
                        <p class="card-text">There are no events available at the moment.</p>
                        @auth
                            @if(auth()->user()->is_admin)
                                <a href="{{ route('events.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Create First Event
                                </a>
                            @endif
                        @endauth
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection