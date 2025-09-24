@extends('layouts.app')

@section('title', $event->getName() . ' - EventPass')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="mb-0">{{ $event->getName() }}</h1>
                @auth
                    @if(auth()->user()->is_admin)
                        <div class="btn-group" role="group">
                            <a href="{{ route('events.edit', $event) }}" class="btn btn-outline-primary">
                                <i class="fas fa-edit"></i> Edit Event
                            </a>
                            <form method="POST" action="{{ route('events.destroy', $event) }}" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger" 
                                        onclick="return confirm('Are you sure you want to delete this event?')">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </form>
                        </div>
                    @endif
                @endauth
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Event Details</h5>
                    <p class="card-text">{{ $event->getDescription() }}</p>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <h6><i class="fas fa-calendar"></i> Start Time</h6>
                            <p>{{ $event->getStartTime()->format('M d, Y H:i') }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6><i class="fas fa-calendar"></i> End Time</h6>
                            <p>{{ $event->getEndTime()->format('M d, Y H:i') }}</p>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <h6><i class="fas fa-map-marker-alt"></i> Venue</h6>
                            <p>{{ $event->venue?->getName() ?? 'TBD' }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6><i class="fas fa-users"></i> Capacity</h6>
                            <p>{{ $event->getCapacity() }} seats</p>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <h6><i class="fas fa-info-circle"></i> Status</h6>
                            <span class="badge badge-{{ $event->getStatus()->value === 'published' ? 'success' : 'secondary' }}">
                                {{ ucfirst($event->getStatus()->value) }}
                            </span>
                        </div>
                        <div class="col-md-6">
                            <h6><i class="fas fa-ticket-alt"></i> Available</h6>
                            <p>{{ $event->availableCapacity() }} seats available</p>
                        </div>
                    </div>
                </div>
            </div>

            @if($event->ticketTypes->count() > 0)
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Ticket Types</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach($event->ticketTypes as $ticketType)
                                <div class="col-md-6 mb-3">
                                    <div class="card">
                                        <div class="card-body">
                                            <h6 class="card-title">{{ $ticketType->getName() }}</h6>
                                            <p class="card-text">
                                                <strong>Price:</strong> ${{ number_format($ticketType->getPrice(), 2) }}<br>
                                                <strong>Quantity:</strong> {{ $ticketType->getQuantity() }}
                                            </p>
                                            @auth
                                                <button class="btn btn-primary btn-sm" onclick="addToCart({{ $ticketType->getId() }})">
                                                    <i class="fas fa-shopping-cart"></i> Add to Cart
                                                </button>
                                            @else
                                                <a href="{{ route('login') }}" class="btn btn-primary btn-sm">
                                                    <i class="fas fa-sign-in-alt"></i> Login to Buy
                                                </a>
                                            @endauth
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('events.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Events
                        </a>
                        
                        @auth
                            @if(auth()->user()->is_admin)
                                <a href="{{ route('admin.events.edit', $event) }}" class="btn btn-outline-primary">
                                    <i class="fas fa-edit"></i> Admin Edit
                                </a>
                            @endif
                        @endauth
                        
                        <button class="btn btn-outline-info" onclick="shareEvent()">
                            <i class="fas fa-share"></i> Share Event
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function addToCart(ticketTypeId) {
    // Implementar lógica de carrito
    alert('Ticket type ' + ticketTypeId + ' added to cart!');
}

function shareEvent() {
    if (navigator.share) {
        navigator.share({
            title: '{{ $event->getName() }}',
            text: '{{ Str::limit($event->getDescription(), 100) }}',
            url: window.location.href
        });
    } else {
        // Fallback: copiar URL al clipboard
        navigator.clipboard.writeText(window.location.href);
        alert('Event URL copied to clipboard!');
    }
}
</script>
@endsection