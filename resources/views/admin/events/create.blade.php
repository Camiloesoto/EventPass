@extends('layouts.admin')

@section('title', 'Create Event - Admin Panel')
@section('page-title', 'Create New Event')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Event Details</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.events.store') }}">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name" class="form-label">Event Name *</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name') }}" required>
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
                                                {{ old('venue_id') == $venue->getId() ? 'selected' : '' }}>
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
                                  id="description" name="description" rows="4" required>{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="start_time" class="form-label">Start Time *</label>
                                <input type="datetime-local" class="form-control @error('start_time') is-invalid @enderror" 
                                       id="start_time" name="start_time" value="{{ old('start_time') }}" required>
                                @error('start_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="end_time" class="form-label">End Time *</label>
                                <input type="datetime-local" class="form-control @error('end_time') is-invalid @enderror" 
                                       id="end_time" name="end_time" value="{{ old('end_time') }}" required>
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
                                       id="capacity" name="capacity" value="{{ old('capacity') }}" 
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
                                    <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                                    <option value="published" {{ old('status') == 'published' ? 'selected' : '' }}>Published</option>
                                    <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                    <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>Completed</option>
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
                            <i class="fas fa-save"></i> Create Event
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Event Guidelines</h5>
            </div>
            <div class="card-body">
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <i class="fas fa-info-circle text-primary"></i>
                        <strong>Draft:</strong> Event is being prepared, not public
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-check-circle text-success"></i>
                        <strong>Published:</strong> Event is public and tickets can be sold
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-times-circle text-danger"></i>
                        <strong>Cancelled:</strong> Event was cancelled
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-flag-checkered text-primary"></i>
                        <strong>Completed:</strong> Event has concluded
                    </li>
                </ul>
                
                <hr>
                
                <h6>Tips:</h6>
                <ul class="list-unstyled">
                    <li class="mb-1">• Set realistic capacity numbers</li>
                    <li class="mb-1">• Use descriptive event names</li>
                    <li class="mb-1">• Include all relevant details in description</li>
                    <li class="mb-1">• Verify venue availability</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
