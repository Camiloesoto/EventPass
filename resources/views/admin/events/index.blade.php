@extends('layouts.admin')

@section('title', 'Events Management - Admin Panel')
@section('page-title', 'Events Management')

@section('page-actions')
    <a href="{{ route('admin.events.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Create New Event
    </a>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">All Events</h5>
            </div>
            <div class="card-body">
                <!-- Filters -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <form method="GET" action="{{ route('admin.events.index') }}">
                            <div class="input-group">
                                <input type="text" name="search" class="form-control" 
                                       placeholder="Search events..." 
                                       value="{{ request('search') }}">
                                <button class="btn btn-outline-secondary" type="submit">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                    <div class="col-md-6">
                        <form method="GET" action="{{ route('admin.events.index') }}">
                            <div class="input-group">
                                <select name="status" class="form-select">
                                    <option value="">All Statuses</option>
                                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                                    <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Published</option>
                                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                </select>
                                <button class="btn btn-outline-secondary" type="submit">
                                    <i class="fas fa-filter"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Events Table -->
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
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
                            @forelse($events as $event)
                                <tr>
                                    <td>{{ $event->getId() }}</td>
                                    <td>
                                        <strong>{{ $event->getName() }}</strong>
                                        <br>
                                        <small class="text-muted">{{ Str::limit($event->getDescription(), 50) }}</small>
                                    </td>
                                    <td>{{ $event->venue?->getName() ?? 'No venue' }}</td>
                                    <td>{{ $event->getStartTime()->format('M d, Y H:i') }}</td>
                                    <td>
                                        <span class="badge bg-info">{{ $event->availableCapacity() }}</span>
                                        / {{ $event->getCapacity() }}
                                    </td>
                                    <td>
                                        @switch($event->getStatus())
                                            @case('draft')
                                                <span class="badge bg-secondary">Draft</span>
                                                @break
                                            @case('published')
                                                <span class="badge bg-success">Published</span>
                                                @break
                                            @case('cancelled')
                                                <span class="badge bg-danger">Cancelled</span>
                                                @break
                                            @case('completed')
                                                <span class="badge bg-primary">Completed</span>
                                                @break
                                        @endswitch
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.events.show', $event) }}" 
                                               class="btn btn-sm btn-outline-info" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.events.edit', $event) }}" 
                                               class="btn btn-sm btn-outline-warning" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form method="POST" action="{{ route('admin.events.destroy', $event) }}" 
                                                  style="display: inline;" 
                                                  onsubmit="return confirm('Are you sure you want to delete this event?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">
                                        <i class="fas fa-calendar-times fa-3x mb-3"></i>
                                        <br>
                                        No events found.
                                        <br>
                                        <a href="{{ route('admin.events.create') }}" class="btn btn-primary mt-2">
                                            Create First Event
                                        </a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($events->hasPages())
                    <div class="d-flex justify-content-center">
                        {{ $events->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
