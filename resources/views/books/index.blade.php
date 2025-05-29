@extends('layouts.app')

@section('title', 'Könyvek')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Könyvek</h1>
    @can('create', App\Models\Book::class)
        <a href="{{ route('books.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Új könyv
        </a>
    @endcan
</div>

<!-- Search and Filter Form -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('books.index') }}" class="row g-3">
            <div class="col-md-4">
                <label for="search" class="form-label">Keresés</label>
                <input type="text" class="form-control" id="search" name="search" 
                       value="{{ request('search') }}" placeholder="Cím, ISBN vagy szerző...">
            </div>
            <div class="col-md-3">
                <label for="category" class="form-label">Kategória</label>
                <select class="form-select" id="category" name="category">
                    <option value="">Összes kategória</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" 
                                {{ request('category') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label for="available" class="form-label">Elérhetőség</label>
                <select class="form-select" id="available" name="available">
                    <option value="">Összes könyv</option>
                    <option value="1" {{ request('available') == '1' ? 'selected' : '' }}>
                        Csak elérhető
                    </option>
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-secondary me-2">
                    <i class="bi bi-search"></i> Keresés
                </button>
                <a href="{{ route('books.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-clockwise"></i>
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Books Grid -->
@if($books->count() > 0)
    <div class="row">
        @foreach($books as $book)
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">{{ $book->title }}</h5>
                        <p class="card-text">
                            <strong>Szerző:</strong> {{ $book->author->name }}<br>
                            <strong>Kategória:</strong> {{ $book->category->name }}<br>
                            <strong>ISBN:</strong> {{ $book->isbn }}<br>
                            <strong>Elérhető:</strong> 
                            @if($book->available_copies > 0)
                                <span class="text-success">{{ $book->available_copies }} db</span>
                            @else
                                <span class="text-danger">Nem elérhető</span>
                            @endif
                        </p>
                    </div>
                    <div class="card-footer">
                        <div class="btn-group w-100" role="group">
                            <a href="{{ route('books.show', $book) }}" class="btn btn-outline-primary btn-sm">
                                <i class="bi bi-eye"></i> Megtekintés
                            </a>
                            @can('update', $book)
                                <a href="{{ route('books.edit', $book) }}" class="btn btn-outline-secondary btn-sm">
                                    <i class="bi bi-pencil"></i> Szerkesztés
                                </a>
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    
    <!-- Pagination -->
    <div class="d-flex justify-content-center">
        {{ $books->withQueryString()->links() }}
    </div>
@else
    <div class="text-center py-5">
        <i class="bi bi-book display-1 text-muted"></i>
        <h3 class="text-muted mt-3">Nincs könyv</h3>
        <p class="text-muted">Még nem adtak hozzá könyvet a rendszerhez.</p>
        @can('create', App\Models\Book::class)
            <a href="{{ route('books.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Első könyv hozzáadása
            </a>
        @endcan
    </div>
@endif
@endsection
