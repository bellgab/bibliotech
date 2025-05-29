@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">Fines Report</h1>
                <a href="{{ route('reports.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Reports
                </a>
            </div>

            <!-- Summary Statistics -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <h5 class="card-title">Total Fines Collected</h5>
                            <h2 class="card-text">${{ number_format($totalFines, 2) }}</h2>
                            <small>From returned books</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <h5 class="card-title">Unpaid Fines</h5>
                            <h2 class="card-text">${{ number_format($unpaidFines, 2) }}</h2>
                            <small>From overdue books</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <h5 class="card-title">Total Outstanding</h5>
                            <h2 class="card-text">${{ number_format($totalFines + $unpaidFines, 2) }}</h2>
                            <small>All time fines</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Current Unpaid Fines (from overdue books) -->
            @if($unpaidFines > 0)
                <div class="card mb-4">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0">
                            <i class="fas fa-exclamation-triangle"></i> Current Unpaid Fines (Overdue Books)
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-warning">
                            <strong>Note:</strong> These fines are calculated for currently overdue books and will be applied when the books are returned.
                        </div>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Book</th>
                                        <th>Borrower</th>
                                        <th>Due Date</th>
                                        <th>Days Overdue</th>
                                        <th>Fine Amount</th>
                                        <th>Contact</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $overdueBorrows = \App\Models\BookBorrowing::whereNull('returned_at')
                                            ->where('due_date', '<', now())
                                            ->with(['book.author', 'user'])
                                            ->orderBy('due_date', 'asc')
                                            ->get();
                                    @endphp
                                    @foreach($overdueBorrows as $borrow)
                                        @php
                                            $daysOverdue = now()->diffInDays($borrow->due_date);
                                            $calculatedFine = $borrow->calculateFine();
                                        @endphp
                                        <tr class="table-warning">
                                            <td>
                                                <a href="{{ route('books.show', $borrow->book) }}" class="text-decoration-none">
                                                    {{ $borrow->book->title }}
                                                </a>
                                                <br>
                                                <small class="text-muted">by {{ $borrow->book->author->name ?? 'Unknown' }}</small>
                                            </td>
                                            <td>
                                                <a href="{{ route('users.show', $borrow->user) }}" class="text-decoration-none">
                                                    {{ $borrow->user->name }}
                                                </a>
                                                <br>
                                                <small class="text-muted">{{ $borrow->user->email }}</small>
                                            </td>
                                            <td>{{ $borrow->due_date->format('M d, Y') }}</td>
                                            <td>
                                                <span class="badge bg-danger">
                                                    {{ $daysOverdue }} {{ $daysOverdue == 1 ? 'day' : 'days' }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="fw-bold text-danger">
                                                    ${{ number_format($calculatedFine, 2) }}
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

            <!-- Fines History -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Fines History (Paid)</h5>
                </div>
                <div class="card-body">
                    @if($finesData->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Book</th>
                                        <th>Borrower</th>
                                        <th>Borrowed Date</th>
                                        <th>Due Date</th>
                                        <th>Returned Date</th>
                                        <th>Days Late</th>
                                        <th>Fine Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($finesData as $borrow)
                                        @php
                                            $daysLate = $borrow->returned_at && $borrow->due_date 
                                                ? $borrow->returned_at->diffInDays($borrow->due_date) 
                                                : 0;
                                        @endphp
                                        <tr>
                                            <td>
                                                <a href="{{ route('books.show', $borrow->book) }}" class="text-decoration-none">
                                                    {{ $borrow->book->title }}
                                                </a>
                                                <br>
                                                <small class="text-muted">by {{ $borrow->book->author->name ?? 'Unknown' }}</small>
                                            </td>
                                            <td>
                                                <a href="{{ route('users.show', $borrow->user) }}" class="text-decoration-none">
                                                    {{ $borrow->user->name }}
                                                </a>
                                                <br>
                                                <small class="text-muted">{{ $borrow->user->email }}</small>
                                            </td>
                                            <td>{{ $borrow->borrowed_at->format('M d, Y') }}</td>
                                            <td>{{ $borrow->due_date->format('M d, Y') }}</td>
                                            <td>
                                                {{ $borrow->returned_at ? $borrow->returned_at->format('M d, Y') : 'Not returned' }}
                                            </td>
                                            <td>
                                                @if($daysLate > 0)
                                                    <span class="badge bg-warning">
                                                        {{ $daysLate }} {{ $daysLate == 1 ? 'day' : 'days' }}
                                                    </span>
                                                @else
                                                    <span class="badge bg-success">On time</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="fw-bold text-success">
                                                    ${{ number_format($borrow->fine_amount, 2) }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination could go here if needed -->
                        @if($finesData->count() >= 50)
                            <div class="mt-3">
                                <p class="text-muted">Showing recent fines. Contact admin for complete history.</p>
                            </div>
                        @endif
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-dollar-sign text-muted" style="font-size: 3rem;"></i>
                            <p class="text-muted mt-3">No fines have been collected yet.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Fine Statistics -->
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Fine Summary</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span>Books with Fines:</span>
                                <span class="fw-bold">{{ $finesData->count() }}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span>Average Fine:</span>
                                <span class="fw-bold">
                                    ${{ $finesData->count() > 0 ? number_format($finesData->avg('fine_amount'), 2) : '0.00' }}
                                </span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span>Highest Fine:</span>
                                <span class="fw-bold text-danger">
                                    ${{ $finesData->count() > 0 ? number_format($finesData->max('fine_amount'), 2) : '0.00' }}
                                </span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span>Total Collected:</span>
                                <span class="fw-bold text-success">
                                    ${{ number_format($totalFines, 2) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Outstanding Fines</h5>
                        </div>
                        <div class="card-body">
                            @php
                                $currentOverdue = \App\Models\BookBorrowing::whereNull('returned_at')
                                    ->where('due_date', '<', now())
                                    ->count();
                            @endphp
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span>Overdue Books:</span>
                                <span class="fw-bold text-warning">{{ $currentOverdue }}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span>Estimated Fines:</span>
                                <span class="fw-bold text-warning">
                                    ${{ number_format($unpaidFines, 2) }}
                                </span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span>When Collected:</span>
                                <span class="fw-bold text-info">
                                    ${{ number_format($totalFines + $unpaidFines, 2) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
