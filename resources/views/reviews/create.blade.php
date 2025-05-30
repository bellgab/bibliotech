@extends('layouts.app')

@section('title', 'Új értékelés')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Új könyv értékelés</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('reviews.store') }}">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="book_id" class="form-label">Könyv <span class="text-danger">*</span></label>
                            @if($book)
                                <div class="card">
                                    <div class="card-body p-3">
                                        <h6 class="mb-1">{{ $book->title }}</h6>
                                        <p class="text-muted mb-0">{{ $book->author->name }}</p>
                                    </div>
                                </div>
                                <input type="hidden" name="book_id" value="{{ $book->id }}">
                            @else
                                <select class="form-select @error('book_id') is-invalid @enderror" 
                                        id="book_id" name="book_id" required>
                                    <option value="">Válassz könyvet...</option>
                                    @foreach($books as $bookOption)
                                        <option value="{{ $bookOption->id }}" 
                                                {{ old('book_id') == $bookOption->id ? 'selected' : '' }}>
                                            {{ $bookOption->title }} - {{ $bookOption->author->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('book_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            @endif
                        </div>

                        <div class="mb-3">
                            <label for="rating" class="form-label">Értékelés <span class="text-danger">*</span></label>
                            <div class="rating-input">
                                @for($i = 1; $i <= 5; $i++)
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="rating" 
                                               id="rating{{ $i }}" value="{{ $i }}" 
                                               {{ old('rating') == $i ? 'checked' : '' }} required>
                                        <label class="form-check-label" for="rating{{ $i }}">
                                            {{ $i }} csillag
                                            @if($i == 1) (Rossz) @endif
                                            @if($i == 2) (Gyenge) @endif
                                            @if($i == 3) (Átlagos) @endif
                                            @if($i == 4) (Jó) @endif
                                            @if($i == 5) (Kiváló) @endif
                                        </label>
                                    </div>
                                @endfor
                            </div>
                            @error('rating')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                            
                            <!-- Visual star rating display -->
                            <div class="mt-2">
                                <span class="rating-display text-warning fs-4">
                                    <i class="far fa-star" data-rating="1"></i>
                                    <i class="far fa-star" data-rating="2"></i>
                                    <i class="far fa-star" data-rating="3"></i>
                                    <i class="far fa-star" data-rating="4"></i>
                                    <i class="far fa-star" data-rating="5"></i>
                                </span>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="comment" class="form-label">Megjegyzés</label>
                            <textarea class="form-control @error('comment') is-invalid @enderror" 
                                      id="comment" name="comment" rows="5" 
                                      placeholder="Írj részletes értékelést a könyvről...">{{ old('comment') }}</textarea>
                            <div class="form-text">Minimum 10, maximum 1000 karakter.</div>
                            @error('comment')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            <strong>Fontos:</strong> Az értékelésed moderációra kerül, és csak jóváhagyás után jelenik meg nyilvánosan.
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('reviews.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Vissza
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Értékelés mentése
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const ratingInputs = document.querySelectorAll('input[name="rating"]');
    const ratingStars = document.querySelectorAll('.rating-display i');
    
    function updateStarDisplay(rating) {
        ratingStars.forEach((star, index) => {
            if (index < rating) {
                star.className = 'fas fa-star';
            } else {
                star.className = 'far fa-star';
            }
        });
    }
    
    // Handle radio button changes
    ratingInputs.forEach(input => {
        input.addEventListener('change', function() {
            updateStarDisplay(parseInt(this.value));
        });
    });
    
    // Handle star clicks
    ratingStars.forEach((star, index) => {
        star.addEventListener('click', function() {
            const rating = index + 1;
            document.getElementById('rating' + rating).checked = true;
            updateStarDisplay(rating);
        });
        
        star.style.cursor = 'pointer';
    });
    
    // Initialize with current value
    const checkedRating = document.querySelector('input[name="rating"]:checked');
    if (checkedRating) {
        updateStarDisplay(parseInt(checkedRating.value));
    }
});
</script>
@endsection
