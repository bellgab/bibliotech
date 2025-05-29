@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">Books Report</h1>
                <a href="{{ route('reports.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Reports
                </a>
            </div>

            <!-- Summary Statistics -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <h5 class="card-title">Total Books</h5>
                            <h2 class="card-text">{{ $books->count() }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <h5 class="card-title">Total Borrows</h5>
                            <h2 class="card-text">{{ $books->sum('borrows_count') }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <h5 class="card-title">Average Borrows per Book</h5>
                            <h2 class="card-text">{{ $books->count() > 0 ? round($books->sum('borrows_count') / $books->count(), 1) : 0 }}</h2>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Most Popular Books -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Most Popular Books (Top 10)</h5>
                </div>
                <div class="card-body">
                    @if($mostPopular->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Rank</th>
                                        <th>Title</th>
                                        <th>Author</th>
                                        <th>Category</th>
                                        <th>Times Borrowed</th>
                                        <th>Available Copies</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($mostPopular as $index => $book)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>
                                                <a href="{{ route('books.show', $book) }}" class="text-decoration-none">
                                                    {{ $book->title }}
                                                </a>
                                            </td>
                                            <td>{{ $book->author->name ?? 'Unknown' }}</td>
                                            <td>{{ $book->category->name ?? 'Uncategorized' }}</td>
                                            <td>
                                                <span class="badge bg-primary">{{ $book->borrows_count }}</span>
                                            </td>
                                            <td>{{ $book->available_copies }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted">No books found.</p>
                    @endif
                </div>
            </div>

            <!-- Least Popular Books -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Least Popular Books (Bottom 10)</h5>
                </div>
                <div class="card-body">
                    @if($leastPopular->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>Author</th>
                                        <th>Category</th>
                                        <th>Times Borrowed</th>
                                        <th>Available Copies</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($leastPopular as $book)
                                        <tr>
                                            <td>
                                                <a href="{{ route('books.show', $book) }}" class="text-decoration-none">
                                                    {{ $book->title }}
                                                </a>
                                            </td>
                                            <td>{{ $book->author->name ?? 'Unknown' }}</td>
                                            <td>{{ $book->category->name ?? 'Uncategorized' }}</td>
                                            <td>
                                                <span class="badge bg-{{ $book->borrows_count == 0 ? 'danger' : 'warning' }}">
                                                    {{ $book->borrows_count }}
                                                </span>
                                            </td>
                                            <td>{{ $book->available_copies }}</td>
                                            <td>
                                                @if($book->borrows_count == 0)
                                                    <span class="badge bg-danger">Never Borrowed</span>
                                                @else
                                                    <span class="badge bg-warning">Low Interest</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted">No books found.</p>
                    @endif
                </div>
            </div>

            <!-- All Books Table -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">All Books</h5>
                </div>
                <div class="card-body">
                    @if($books->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped table-sm">
                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>Author</th>
                                        <th>Category</th>
                                        <th>ISBN</th>
                                        <th>Total Copies</th>
                                        <th>Available</th>
                                        <th>Times Borrowed</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($books as $book)
                                        <tr>
                                            <td>
                                                <a href="{{ route('books.show', $book) }}" class="text-decoration-none">
                                                    {{ $book->title }}
                                                </a>
                                            </td>
                                            <td>{{ $book->author->name ?? 'Unknown' }}</td>
                                            <td>{{ $book->category->name ?? 'Uncategorized' }}</td>
                                            <td>{{ $book->isbn ?? 'N/A' }}</td>
                                            <td>{{ $book->total_copies }}</td>
                                            <td>{{ $book->available_copies }}</td>
                                            <td>
                                                <span class="badge bg-info">{{ $book->borrows_count }}</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted">No books found.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
