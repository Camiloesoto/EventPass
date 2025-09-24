@extends('layouts.admin')

@section('title', 'Edit Event - Admin Panel')
@section('page-title', 'Edit Event: ' . $event->getName())

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Event Details</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.events.update', $event) }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name" class="form-label">Event Name *</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name', $event->getName()) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="venue_id" class="form-label">Venue</label>
                                <select class="form-select @error('venue_id') is-invalid @enderror" 
                                        id="venue_id" name="venue_id">
                                    <option value="">Select Venue</option>
                                    @foreach($venues as $venue)
                                        <option value="{{ $venue->getId() }}" 
                                                {{ old('venue_id', $event->venue_id) == $venue->getId() ? 'selected' : '' }}>
                                            {{ $venue->getName() }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('venue_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description *</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" name="description" rows="4" required>{{ old('description', $event->getDescription()) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="start_time" class="form-label">Start Time *</label>
                                <input type="datetime-local" class="form-control @error('start_time') is-invalid @enderror" 
                                       id="start_time" name="start_time" 
                                       value="{{ old('start_time', $event->getStartTime()->format('Y-m-d\TH:i')) }}" required>
                                @error('start_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="end_time" class="form-label">End Time *</label>
                                <input type="datetime-local" class="form-control @error('end_time') is-invalid @enderror" 
                                       id="end_time" name="end_time" 
                                       value="{{ old('end_time', $event->getEndTime()->format('Y-m-d\TH:i')) }}" required>
                                @error('end_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="capacity" class="form-label">Capacity *</label>
                                <input type="number" class="form-control @error('capacity') is-invalid @enderror" 
                                       id="capacity" name="capacity" value="{{ old('capacity', $event->getCapacity()) }}" 
                                       min="1" required>
                                @error('capacity')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="status" class="form-label">Status *</label>
                                <select class="form-select @error('status') is-invalid @enderror" 
                                        id="status" name="status" required>
                                    <option value="">Select Status</option>
                                    <option value="draft" {{ old('status', $event->getStatus()) == 'draft' ? 'selected' : '' }}>Draft</option>
                                    <option value="published" {{ old('status', $event->getStatus()) == 'published' ? 'selected' : '' }}>Published</option>
                                    <option value="cancelled" {{ old('status', $event->getStatus()) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                    <option value="completed" {{ old('status', $event->getStatus()) == 'completed' ? 'selected' : '' }}>Completed</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.events.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Events
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update Event
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Event Statistics</h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6">
                        <div class="border rounded p-3 mb-3">
                            <h4 class="text-primary">{{ $event->availableCapacity() }}</h4>
                            <small class="text-muted">Available</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="border rounded p-3 mb-3">
                            <h4 class="text-success">{{ $event->getCapacity() - $event->availableCapacity() }}</h4>
                            <small class="text-muted">Sold</small>
                        </div>
                    </div>
                </div>
                
                <hr>
                
                <h6>Event Info:</h6>
                <ul class="list-unstyled">
                    <li class="mb-1">
                        <strong>Created:</strong> {{ $event->getCreatedAt()->format('M d, Y') }}
                    </li>
                    <li class="mb-1">
                        <strong>Last Updated:</strong> {{ $event->getUpdatedAt()->format('M d, Y H:i') }}
                    </li>
                    <li class="mb-1">
                        <strong>Status:</strong> 
                        <span class="badge bg-{{ $event->getStatus() === 'published' ? 'success' : 'secondary' }}">
                            {{ ucfirst($event->getStatus()) }}
                        </span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
