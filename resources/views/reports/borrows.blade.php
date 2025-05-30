@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">Borrows Report</h1>
                <a href="{{ route('reports.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Reports
                </a>
            </div>

            <!-- Summary Statistics -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <h5 class="card-title">This Month</h5>
                            <h2 class="card-text">{{ $monthlyBorrows }}</h2>
                            <small>New borrows</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <h5 class="card-title">Last Month</h5>
                            <h2 class="card-text">{{ $lastMonthBorrows }}</h2>
                            <small>
                                @if($lastMonthBorrows > 0)
                                    @php
                                        $change = (($monthlyBorrows - $lastMonthBorrows) / $lastMonthBorrows) * 100;
                                    @endphp
                                    {{ $change > 0 ? '+' : '' }}{{ round($change, 1) }}% change
                                @else
                                    New activity
                                @endif
                            </small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <h5 class="card-title">Active Borrows</h5>
                            <h2 class="card-text">{{ $activeBorrows->count() }}</h2>
                            <small>Currently borrowed</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-danger text-white">
                        <div class="card-body">
                            <h5 class="card-title">Overdue</h5>
                            <h2 class="card-text">{{ $overdueBorrows->count() }}</h2>
                            <small>Need attention</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Overdue Books -->
            @if($overdueBorrows->count() > 0)
                <div class="card mb-4">
                    <div class="card-header bg-danger text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-exclamation-triangle"></i> Overdue Books
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Book</th>
                                        <th>Author</th>
                                        <th>Borrower</th>
                                        <th>Due Date</th>
                                        <th>Days Overdue</th>
                                        <th>Contact</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($overdueBorrows as $borrow)
                                        @php
                                            $daysOverdue = now()->diffInDays($borrow->due_date);
                                        @endphp
                                        <tr class="table-danger">
                                            <td>
                                                <a href="{{ route('books.show', $borrow->book) }}" class="text-decoration-none">
                                                    {{ $borrow->book->title }}
                                                </a>
                                            </td>
                                            <td>{{ $borrow->book->author->name ?? 'Unknown' }}</td>
                                            <td>
                                                <a href="{{ route('users.show', $borrow->user) }}" class="text-decoration-none">
                                                    {{ $borrow->user->name }}
                                                </a>
                                            </td>
                                            <td>{{ $borrow->due_date->format('M d, Y') }}</td>
                                            <td>
                                                <span class="badge bg-danger">
                                                    {{ $daysOverdue }} {{ $daysOverdue == 1 ? 'day' : 'days' }}
                                                </span>
                                            </td>
                                            <td>
                                                <a href="mailto:{{ $borrow->user->email }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-envelope"></i>
                                                </a>
                                            </td>
                                            <td>
                                                <a href="{{ route('borrows.show', $borrow) }}" class="btn btn-sm btn-outline-secondary">
                                                    View
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Active Borrows -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Active Borrows</h5>
                </div>
                <div class="card-body">
                    @if($activeBorrows->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Book</th>
                                        <th>Author</th>
                                        <th>Borrower</th>
                                        <th>Borrowed Date</th>
                                        <th>Due Date</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($activeBorrows as $borrow)
                                        @php
                                            $isOverdue = $borrow->due_date < now();
                                            $daysUntilDue = now()->diffInDays($borrow->due_date, false);
                                        @endphp
                                        <tr class="{{ $isOverdue ? 'table-danger' : ($daysUntilDue <= 3 ? 'table-warning' : '') }}">
                                            <td>
                                                <a href="{{ route('books.show', $borrow->book) }}" class="text-decoration-none">
                                                    {{ $borrow->book->title }}
                                                </a>
                                            </td>
                                            <td>{{ $borrow->book->author->name ?? 'Unknown' }}</td>
                                            <td>
                                                <a href="{{ route('users.show', $borrow->user) }}" class="text-decoration-none">
                                                    {{ $borrow->user->name }}
                                                </a>
                                            </td>
                                            <td>{{ $borrow->borrowed_at->format('M d, Y') }}</td>
                                            <td>{{ $borrow->due_date->format('M d, Y') }}</td>
                                            <td>
                                                @if($isOverdue)
                                                    <span class="badge bg-danger">Overdue</span>
                                                @elseif($daysUntilDue <= 3)
                                                    <span class="badge bg-warning">Due Soon</span>
                                                @else
                                                    <span class="badge bg-success">Active</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('borrows.show', $borrow) }}" class="btn btn-sm btn-outline-secondary">
                                                        View
                                                    </a>
                                                    @can('update', $borrow)
                                                        <form action="{{ route('borrows.return', $borrow) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="btn btn-sm btn-outline-success"
                                                                    onclick="return confirm('Mark this book as returned?')">
                                                                Return
                                                            </button>
                                                        </form>
                                                    @endcan
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-book text-muted" style="font-size: 3rem;"></i>
                            <p class="text-muted mt-3">No active borrows at the moment.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Borrowing Trends -->
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Monthly Comparison</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span>This Month:</span>
                                <span class="fw-bold text-primary">{{ $monthlyBorrows }} borrows</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span>Last Month:</span>
                                <span class="fw-bold text-info">{{ $lastMonthBorrows }} borrows</span>
                            </div>
                            @if($lastMonthBorrows > 0)
                                @php
                                    $change = (($monthlyBorrows - $lastMonthBorrows) / $lastMonthBorrows) * 100;
                                @endphp
                                <div class="d-flex justify-content-between align-items-center">
                                    <span>Change:</span>
                                    <span class="fw-bold text-{{ $change >= 0 ? 'success' : 'danger' }}">
                                        {{ $change > 0 ? '+' : '' }}{{ round($change, 1) }}%
                                    </span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Status Overview</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span>On Time:</span>
                                <span class="fw-bold text-success">{{ $activeBorrows->filter(function($b) { return $b->due_date >= now(); })->count() }}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span>Due Soon (3 days):</span>
                                <span class="fw-bold text-warning">{{ $activeBorrows->filter(function($b) { return $b->due_date >= now() && now()->diffInDays($b->due_date) <= 3; })->count() }}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span>Overdue:</span>
                                <span class="fw-bold text-danger">{{ $overdueBorrows->count() }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
