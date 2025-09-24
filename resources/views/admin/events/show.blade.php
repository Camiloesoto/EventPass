@extends('layouts.admin')

@section('title', $event->getName() . ' - Admin Panel')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="mb-0">{{ $event->getName() }}</h1>
                <div class="btn-group" role="group">
                    <a href="{{ route('admin.events.edit', $event) }}" class="btn btn-outline-primary">
                        <i class="fas fa-edit"></i> Edit Event
                    </a>
                    <form method="POST" action="{{ route('admin.events.destroy', $event) }}" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger" 
                                onclick="return confirm('Are you sure you want to delete this event?')">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Event Details</h5>
                </div>
                <div class="card-body">
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
                            @if($event->venue)
                                <small class="text-muted">{{ $event->venue->getAddress() }}</small>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <h6><i class="fas fa-users"></i> Capacity</h6>
                            <p>{{ $event->getCapacity() }} seats</p>
                            <small class="text-muted">{{ $event->availableCapacity() }} available</small>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <h6><i class="fas fa-info-circle"></i> Status</h6>
                            <span class="badge badge-{{ $event->getStatus() === 'published' ? 'success' : ($event->getStatus() === 'draft' ? 'warning' : 'secondary') }}">
                                {{ ucfirst($event->getStatus()) }}
                            </span>
                        </div>
                        <div class="col-md-6">
                            <h6><i class="fas fa-chart-bar"></i> Sales Report</h6>
                            @php $report = $event->salesReport(); @endphp
                            <p>{{ $report->sold }} tickets sold</p>
                            <small class="text-muted">${{ number_format($report->revenue, 2) }} revenue</small>
                        </div>
                    </div>
                    
                    <div class="mt-3">
                        <h6><i class="fas fa-align-left"></i> Description</h6>
                        <p>{{ $event->getDescription() }}</p>
                    </div>
                </div>
            </div>

            @if($event->ticketTypes->count() > 0)
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Ticket Types</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Price</th>
                                        <th>Quantity</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($event->ticketTypes as $ticketType)
                                        <tr>
                                            <td>{{ $ticketType->getName() }}</td>
                                            <td>${{ number_format($ticketType->getPrice(), 2) }}</td>
                                            <td>{{ $ticketType->getQuantity() }}</td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <button class="btn btn-sm btn-outline-primary" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-outline-danger" title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
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
                        <a href="{{ route('admin.events.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Events
                        </a>
                        
                        <a href="{{ route('admin.events.edit', $event) }}" class="btn btn-outline-primary">
                            <i class="fas fa-edit"></i> Edit Event
                        </a>
                        
                        <button class="btn btn-outline-success" onclick="duplicateEvent()">
                            <i class="fas fa-copy"></i> Duplicate Event
                        </button>
                        
                        <button class="btn btn-outline-info" onclick="exportEvent()">
                            <i class="fas fa-download"></i> Export Data
                        </button>
                    </div>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0">Statistics</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <h4 class="text-primary">{{ $event->getCapacity() }}</h4>
                            <small>Total Capacity</small>
                        </div>
                        <div class="col-6">
                            <h4 class="text-success">{{ $event->availableCapacity() }}</h4>
                            <small>Available</small>
                        </div>
                    </div>
                    <hr>
                    <div class="row text-center">
                        <div class="col-6">
                            <h4 class="text-info">{{ $event->ticketTypes->count() }}</h4>
                            <small>Ticket Types</small>
                        </div>
                        <div class="col-6">
                            <h4 class="text-warning">{{ $event->waitlistEntries->count() }}</h4>
                            <small>Waitlist</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function duplicateEvent() {
    if (confirm('Do you want to duplicate this event?')) {
        // Implement duplication logic
        alert('Event duplication feature coming soon!');
    }
}

function exportEvent() {
    // Implement export logic
    alert('Event export feature coming soon!');
}
</script>
@endsection
