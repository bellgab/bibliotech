@extends('layouts.app')

@section('title', 'Új kölcsönzés')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">Új kölcsönzés</h4>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('borrows.store') }}">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="book_id" class="form-label">Könyv *</label>
                                <select class="form-select @error('book_id') is-invalid @enderror" 
                                        id="book_id" name="book_id" required>
                                    <option value="">Válasszon könyvet</option>
                                    @foreach($books as $book)
                                        <option value="{{ $book->id }}" 
                                                {{ old('book_id', request('book_id')) == $book->id ? 'selected' : '' }}
                                                {{ $book->available_copies <= 0 ? 'disabled' : '' }}>
                                            {{ $book->title }} - {{ $book->author->name }}
                                            ({{ $book->available_copies }} db elérhető)
                                        </option>
                                    @endforeach
                                </select>
                                @error('book_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="user_id" class="form-label">Kölcsönző *</label>
                                <select class="form-select @error('user_id') is-invalid @enderror" 
                                        id="user_id" name="user_id" required>
                                    <option value="">Válasszon felhasználót</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" 
                                                {{ old('user_id', request('user_id')) == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }} ({{ $user->email }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('user_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="borrowed_at" class="form-label">Kölcsönzés dátuma *</label>
                                <input type="date" class="form-control @error('borrowed_at') is-invalid @enderror" 
                                       id="borrowed_at" name="borrowed_at" 
                                       value="{{ old('borrowed_at', date('Y-m-d')) }}" required>
                                @error('borrowed_at')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="due_date" class="form-label">Lejárat dátuma *</label>
                                <input type="date" class="form-control @error('due_date') is-invalid @enderror" 
                                       id="due_date" name="due_date" 
                                       value="{{ old('due_date', date('Y-m-d', strtotime('+2 weeks'))) }}" required>
                                @error('due_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="notes" class="form-label">Megjegyzések</label>
                        <textarea class="form-control @error('notes') is-invalid @enderror" 
                                  id="notes" name="notes" rows="3">{{ old('notes') }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('borrows.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Vissza
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check"></i> Kölcsönzés rögzítése
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Automatically set due date to 2 weeks from borrowed date
    const borrowedAtInput = document.getElementById('borrowed_at');
    const dueDateInput = document.getElementById('due_date');
    
    borrowedAtInput.addEventListener('change', function() {
        const borrowedDate = new Date(this.value);
        const dueDate = new Date(borrowedDate);
        dueDate.setDate(dueDate.getDate() + 14); // Add 2 weeks
        
        const dueDateString = dueDate.toISOString().split('T')[0];
        dueDateInput.value = dueDateString;
    });
});
</script>
@endsection
