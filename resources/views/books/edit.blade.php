@extends('layouts.app')

@section('title', 'Könyv szerkesztése')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">Könyv szerkesztése: {{ $book->title }}</h4>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('books.update', $book) }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="title" class="form-label">Cím *</label>
                                <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                       id="title" name="title" value="{{ old('title', $book->title) }}" required>
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="author_id" class="form-label">Szerző *</label>
                                <select class="form-select @error('author_id') is-invalid @enderror" 
                                        id="author_id" name="author_id" required>
                                    <option value="">Válasszon szerzőt</option>
                                    @foreach($authors as $author)
                                        <option value="{{ $author->id }}" 
                                                {{ old('author_id', $book->author_id) == $author->id ? 'selected' : '' }}>
                                            {{ $author->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('author_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="category_id" class="form-label">Kategória *</label>
                                <select class="form-select @error('category_id') is-invalid @enderror" 
                                        id="category_id" name="category_id" required>
                                    <option value="">Válasszon kategóriát</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" 
                                                {{ old('category_id', $book->category_id) == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="isbn" class="form-label">ISBN *</label>
                                <input type="text" class="form-control @error('isbn') is-invalid @enderror" 
                                       id="isbn" name="isbn" value="{{ old('isbn', $book->isbn) }}" required>
                                @error('isbn')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Leírás</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" name="description" rows="4">{{ old('description', $book->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="published_year" class="form-label">Kiadás éve</label>
                                <input type="number" class="form-control @error('published_year') is-invalid @enderror" 
                                       id="published_year" name="published_year" value="{{ old('published_year', $book->published_year) }}" 
                                       min="1000" max="{{ date('Y') }}">
                                @error('published_year')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="total_copies" class="form-label">Összes példány *</label>
                                <input type="number" class="form-control @error('total_copies') is-invalid @enderror" 
                                       id="total_copies" name="total_copies" value="{{ old('total_copies', $book->total_copies) }}" 
                                       min="1" required>
                                @error('total_copies')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="available_copies" class="form-label">Elérhető példány *</label>
                                <input type="number" class="form-control @error('available_copies') is-invalid @enderror" 
                                       id="available_copies" name="available_copies" value="{{ old('available_copies', $book->available_copies) }}" 
                                       min="0" required>
                                @error('available_copies')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('books.show', $book) }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Vissza
                        </a>
                        <div>
                            <a href="{{ route('books.index') }}" class="btn btn-outline-secondary me-2">
                                Mégse
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check"></i> Mentés
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
