@extends('layouts.app')

@section('title', 'Értékelés szerkesztése')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Értékelés szerkesztése</h4>
                </div>
                <div class="card-body">
                    <!-- Book Information -->
                    <div class="alert alert-info mb-4">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-book fa-2x me-3"></i>
                            <div>
                                <h6 class="mb-1">{{ $review->book->title }}</h6>
                                <p class="mb-0 text-muted">{{ $review->book->author->name }}</p>
                            </div>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('reviews.update', $review) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="rating" class="form-label">Értékelés <span class="text-danger">*</span></label>
                            <div class="rating-input">
                                @for($i = 1; $i <= 5; $i++)
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="rating" 
                                               id="rating{{ $i }}" value="{{ $i }}" 
                                               {{ (old('rating', $review->rating)) == $i ? 'checked' : '' }} required>
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
                                      placeholder="Írj részletes értékelést a könyvről...">{{ old('comment', $review->comment) }}</textarea>
                            <div class="form-text">Minimum 10, maximum 1000 karakter.</div>
                            @error('comment')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        @if($review->is_approved)
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle"></i>
                                <strong>Figyelem:</strong> Ez az értékelés már jóvá lett hagyva. A módosítás után újra moderációra kerül.
                            </div>
                        @else
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i>
                                <strong>Info:</strong> Az értékelésed még jóváhagyásra vár. A módosítások mentése után továbbra is moderációra fog várni.
                            </div>
                        @endif

                        <div class="d-flex justify-content-between">
                            <div>
                                <a href="{{ route('reviews.show', $review) }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Vissza
                                </a>
                                <a href="{{ route('reviews.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-list"></i> Lista
                                </a>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Módosítások mentése
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Review History -->
            <div class="card mt-4">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-history"></i> Értékelés információk</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <strong>Létrehozva:</strong> {{ $review->created_at->format('Y. m. d. H:i') }}
                        </div>
                        @if($review->updated_at && $review->updated_at != $review->created_at)
                            <div class="col-md-6">
                                <strong>Utoljára módosítva:</strong> {{ $review->updated_at->format('Y. m. d. H:i') }}
                            </div>
                        @endif
                    </div>
                    @if($review->is_approved && $review->approved_at)
                        <div class="row mt-2">
                            <div class="col-md-6">
                                <strong>Jóváhagyva:</strong> {{ $review->approved_at->format('Y. m. d. H:i') }}
                            </div>
                            @if($review->approvedBy)
                                <div class="col-md-6">
                                    <strong>Jóváhagyta:</strong> {{ $review->approvedBy->name }}
                                </div>
                            @endif
                        </div>
                    @endif
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
