@extends('layouts.admin')

@section('title', 'Create User - Admin Panel')
@section('page-title', 'Create New User')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">User Details</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.users.store') }}">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name" class="form-label">Full Name *</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name') }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address *</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                       id="email" name="email" value="{{ old('email') }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="password" class="form-label">Password *</label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                       id="password" name="password" required>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label">Confirm Password *</label>
                                <input type="password" class="form-control" 
                                       id="password_confirmation" name="password_confirmation" required>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_admin" name="is_admin" 
                                   {{ old('is_admin') ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_admin">
                                <i class="fas fa-crown text-warning"></i> Administrator Privileges
                            </label>
                        </div>
                        <small class="form-text text-muted">
                            Administrators can access the admin panel and manage events, users, and tickets.
                        </small>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Users
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Create User
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">User Guidelines</h5>
            </div>
            <div class="card-body">
                <h6>Password Requirements:</h6>
                <ul class="list-unstyled">
                    <li class="mb-1">• Minimum 8 characters</li>
                    <li class="mb-1">• Mix of letters and numbers</li>
                    <li class="mb-1">• Avoid common passwords</li>
                </ul>
                
                <hr>
                
                <h6>Administrator Privileges:</h6>
                <ul class="list-unstyled">
                    <li class="mb-1">• Access to admin panel</li>
                    <li class="mb-1">• Manage events and users</li>
                    <li class="mb-1">• View reports and analytics</li>
                    <li class="mb-1">• Scan and verify tickets</li>
                </ul>
                
                <hr>
                
                <h6>Tips:</h6>
                <ul class="list-unstyled">
                    <li class="mb-1">• Use real email addresses</li>
                    <li class="mb-1">• Set strong passwords</li>
                    <li class="mb-1">• Limit admin privileges</li>
                    <li class="mb-1">• Verify email addresses</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
