@extends('layouts.admin')

@section('title', 'Users Management - Admin Panel')
@section('page-title', 'Users Management')

@section('page-actions')
    <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Create New User
    </a>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">All Users</h5>
            </div>
            <div class="card-body">
                <!-- Filters -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <form method="GET" action="{{ route('admin.users.index') }}">
                            <div class="input-group">
                                <input type="text" name="search" class="form-control" 
                                       placeholder="Search users..." 
                                       value="{{ request('search') }}">
                                <button class="btn btn-outline-secondary" type="submit">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                    <div class="col-md-6">
                        <form method="GET" action="{{ route('admin.users.index') }}">
                            <div class="input-group">
                                <select name="is_admin" class="form-select">
                                    <option value="">All Users</option>
                                    <option value="1" {{ request('is_admin') == '1' ? 'selected' : '' }}>Admins Only</option>
                                    <option value="0" {{ request('is_admin') == '0' ? 'selected' : '' }}>Regular Users</option>
                                </select>
                                <button class="btn btn-outline-secondary" type="submit">
                                    <i class="fas fa-filter"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Users Table -->
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Orders</th>
                                <th>Tickets</th>
                                <th>Joined</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $user)
                                <tr>
                                    <td>{{ $user->getId() }}</td>
                                    <td>
                                        <strong>{{ $user->getName() }}</strong>
                                        @if($user->getIsAdmin())
                                            <i class="fas fa-crown text-warning" title="Administrator"></i>
                                        @endif
                                    </td>
                                    <td>{{ $user->getEmail() }}</td>
                                    <td>
                                        @if($user->getIsAdmin())
                                            <span class="badge bg-warning">Admin</span>
                                        @else
                                            <span class="badge bg-primary">User</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $user->orders_count ?? 0 }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-success">{{ $user->tickets_count ?? 0 }}</span>
                                    </td>
                                    <td>{{ $user->getCreatedAt()->format('M d, Y') }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.users.show', $user) }}" 
                                               class="btn btn-sm btn-outline-info" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.users.edit', $user) }}" 
                                               class="btn btn-sm btn-outline-warning" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @if($user->getId() !== auth()->id())
                                                <form method="POST" action="{{ route('admin.users.destroy', $user) }}" 
                                                      style="display: inline;" 
                                                      onsubmit="return confirm('Are you sure you want to delete this user?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-4">
                                        <i class="fas fa-users fa-3x mb-3"></i>
                                        <br>
                                        No users found.
                                        <br>
                                        <a href="{{ route('admin.users.create') }}" class="btn btn-primary mt-2">
                                            Create First User
                                        </a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($users->hasPages())
                    <div class="d-flex justify-content-center">
                        {{ $users->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
