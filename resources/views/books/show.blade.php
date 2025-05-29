@extends('layouts.app')

@section('title', $book->title)

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0">{{ $book->title }}</h4>
                <div>
                    @can('update', $book)
                        <a href="{{ route('books.edit', $book) }}" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-pencil"></i> Szerkesztés
                        </a>
                    @endcan
                    @can('delete', $book)
                        <form method="POST" action="{{ route('books.destroy', $book) }}" 
                              class="d-inline" onsubmit="return confirm('Biztosan törli ezt a könyvet?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                <i class="bi bi-trash"></i> Törlés
                            </button>
                        </form>
                    @endcan
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th width="30%">Szerző:</th>
                                <td>{{ $book->author->name }}</td>
                            </tr>
                            <tr>
                                <th>Kategória:</th>
                                <td>{{ $book->category->name }}</td>
                            </tr>
                            <tr>
                                <th>ISBN:</th>
                                <td>{{ $book->isbn }}</td>
                            </tr>
                            @if($book->published_year)
                            <tr>
                                <th>Kiadás éve:</th>
                                <td>{{ $book->published_year }}</td>
                            </tr>
                            @endif
                            <tr>
                                <th>Összes példány:</th>
                                <td>{{ $book->total_copies }} db</td>
                            </tr>
                            <tr>
                                <th>Elérhető:</th>
                                <td>
                                    @if($book->available_copies > 0)
                                        <span class="badge bg-success">{{ $book->available_copies }} db</span>
                                    @else
                                        <span class="badge bg-danger">Nem elérhető</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Kölcsönzött:</th>
                                <td>{{ $book->total_copies - $book->available_copies }} db</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        @if($book->description)
                            <h6>Leírás:</h6>
                            <p>{{ $book->description }}</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <!-- Action buttons -->
        <div class="card mb-3">
            <div class="card-header">
                <h6 class="mb-0">Műveletek</h6>
            </div>
            <div class="card-body">
                @if($book->available_copies > 0)
                    @can('create', App\Models\BookBorrowing::class)
                        <a href="{{ route('borrows.create', ['book_id' => $book->id]) }}" 
                           class="btn btn-success w-100 mb-2">
                            <i class="bi bi-book"></i> Kölcsönzés
                        </a>
                    @endcan
                @else
                    <button class="btn btn-secondary w-100 mb-2" disabled>
                        <i class="bi bi-x-circle"></i> Nem elérhető
                    </button>
                @endif
                
                <a href="{{ route('books.index') }}" class="btn btn-outline-secondary w-100">
                    <i class="bi bi-arrow-left"></i> Vissza a listához
                </a>
            </div>
        </div>
        
        <!-- Recent borrowings -->
        @if($book->borrows->count() > 0)
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Legutóbbi kölcsönzések</h6>
                </div>
                <div class="card-body">
                    @foreach($book->borrows()->with('user')->latest()->limit(5)->get() as $borrow)
                        <div class="d-flex justify-content-between align-items-center py-2 
                                    {{ !$loop->last ? 'border-bottom' : '' }}">
                            <div>
                                <small class="text-muted">{{ $borrow->user->name }}</small><br>
                                <small>{{ $borrow->borrowed_at->format('Y.m.d') }}</small>
                            </div>
                            <div>
                                @if($borrow->returned_at)
                                    <span class="badge bg-success">Visszahozva</span>
                                @else
                                    <span class="badge bg-warning">Kölcsönzött</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
