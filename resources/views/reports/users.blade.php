@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">Users Report</h1>
                <a href="{{ route('reports.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Reports
                </a>
            </div>

            <!-- Summary Statistics -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <h5 class="card-title">Total Users</h5>
                            <h2 class="card-text">{{ $users->count() }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <h5 class="card-title">Active Users</h5>
                            <h2 class="card-text">{{ $activeUsers->count() }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <h5 class="card-title">Inactive Users</h5>
                            <h2 class="card-text">{{ $inactiveUsers->count() }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <h5 class="card-title">Total Borrows</h5>
                            <h2 class="card-text">{{ $users->sum('borrowed_books_count') }}</h2>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Top Borrowers -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Top Borrowers</h5>
                </div>
                <div class="card-body">
                    @if($users->where('borrowed_books_count', '>', 0)->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Rank</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Membership Type</th>
                                        <th>Total Borrows</th>
                                        <th>Current Borrows</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($users->where('borrowed_books_count', '>', 0)->take(20) as $index => $user)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>
                                                <a href="{{ route('users.show', $user) }}" class="text-decoration-none">
                                                    {{ $user->name }}
                                                </a>
                                            </td>
                                            <td>{{ $user->email }}</td>
                                            <td>
                                                <span class="badge bg-{{ $user->membership_type === 'premium' ? 'warning' : 'secondary' }}">
                                                    {{ ucfirst($user->membership_type) }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-primary">{{ $user->borrowed_books_count }}</span>
                                            </td>
                                            <td>
                                                <span class="badge bg-info">{{ $user->currently_borrowed_books_count }}</span>
                                            </td>
                                            <td>
                                                @if($user->is_active)
                                                    <span class="badge bg-success">Active</span>
                                                @else
                                                    <span class="badge bg-danger">Inactive</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted">No users have borrowed books yet.</p>
                    @endif
                </div>
            </div>

            <!-- User Activity Statistics -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Users by Membership Type</h5>
                        </div>
                        <div class="card-body">
                            @php
                                $membershipStats = $users->groupBy('membership_type')->map(function($group) {
                                    return $group->count();
                                });
                            @endphp
                            @foreach($membershipStats as $type => $count)
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="badge bg-{{ $type === 'premium' ? 'warning' : 'secondary' }}">
                                        {{ ucfirst($type) }}
                                    </span>
                                    <span class="fw-bold">{{ $count }} users</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Borrowing Activity</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span>Never Borrowed:</span>
                                <span class="fw-bold text-muted">{{ $users->where('borrowed_books_count', 0)->count() }} users</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span>1-5 Books:</span>
                                <span class="fw-bold text-info">{{ $users->whereBetween('borrowed_books_count', [1, 5])->count() }} users</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span>6-15 Books:</span>
                                <span class="fw-bold text-primary">{{ $users->whereBetween('borrowed_books_count', [6, 15])->count() }} users</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span>16+ Books:</span>
                                <span class="fw-bold text-success">{{ $users->where('borrowed_books_count', '>', 15)->count() }} users</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- All Users Table -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">All Users</h5>
                </div>
                <div class="card-body">
                    @if($users->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped table-sm">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Membership</th>
                                        <th>Joined</th>
                                        <th>Total Borrows</th>
                                        <th>Current Borrows</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($users as $user)
                                        <tr>
                                            <td>
                                                <a href="{{ route('users.show', $user) }}" class="text-decoration-none">
                                                    {{ $user->name }}
                                                </a>
                                            </td>
                                            <td>{{ $user->email }}</td>
                                            <td>
                                                <span class="badge bg-{{ $user->membership_type === 'premium' ? 'warning' : 'secondary' }}">
                                                    {{ ucfirst($user->membership_type) }}
                                                </span>
                                            </td>
                                            <td>{{ $user->created_at->format('M d, Y') }}</td>
                                            <td>
                                                <span class="badge bg-primary">{{ $user->borrowed_books_count }}</span>
                                            </td>
                                            <td>
                                                <span class="badge bg-info">{{ $user->currently_borrowed_books_count }}</span>
                                            </td>
                                            <td>
                                                @if($user->is_active)
                                                    <span class="badge bg-success">Active</span>
                                                @else
                                                    <span class="badge bg-danger">Inactive</span>
                                                @endif
                                            </td>
                                            <td>
                                                @can('update', $user)
                                                    <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-outline-primary">
                                                        Edit
                                                    </a>
                                                @endcan
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted">No users found.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
