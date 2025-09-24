@extends('layouts.admin')

@section('title', 'Admin Events - EventPass')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="mb-0">
                    <i class="fas fa-calendar-alt"></i> Events Management
                </h1>
                <a href="{{ route('admin.events.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Create New Event
                </a>
            </div>
        </div>
    </div>

    @if($events->count() > 0)
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">All Events ({{ $events->total() }})</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Venue</th>
                                        <th>Start Time</th>
                                        <th>Capacity</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($events as $event)
                                        <tr>
                                            <td>{{ $event->getId() }}</td>
                                            <td>
                                                <strong>{{ $event->getName() }}</strong>
                                                <br>
                                                <small class="text-muted">{{ Str::limit($event->getDescription(), 50) }}</small>
                                            </td>
                                            <td>{{ $event->venue?->getName() ?? 'TBD' }}</td>
                                            <td>
                                                {{ $event->getStartTime()->format('M d, Y') }}
                                                <br>
                                                <small class="text-muted">{{ $event->getStartTime()->format('H:i') }}</small>
                                            </td>
                                            <td>
                                                <span class="badge badge-info">{{ $event->getCapacity() }}</span>
                                                <br>
                                                <small class="text-muted">{{ $event->availableCapacity() }} available</small>
                                            </td>
                                            <td>
                                                <span class="badge badge-{{ $event->getStatus() === 'published' ? 'success' : ($event->getStatus() === 'draft' ? 'warning' : 'secondary') }}">
                                                    {{ ucfirst($event->getStatus()) }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('admin.events.show', $event) }}" class="btn btn-sm btn-outline-info" title="View">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('admin.events.edit', $event) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form method="POST" action="{{ route('admin.events.destroy', $event) }}" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                                title="Delete" onclick="return confirm('Are you sure you want to delete this event?')">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="d-flex justify-content-center mt-4">
                            {{ $events->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="row">
            <div class="col-12">
                <div class="card text-center py-5">
                    <div class="card-body">
                        <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                        <h3 class="card-title">No Events Found</h3>
                        <p class="card-text">Create your first event to get started.</p>
                        <a href="{{ route('admin.events.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Create First Event
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection