@extends('layouts.app')

@section('title', 'Értékelés részletei')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Értékelés részletei</h4>
                    @if(!$review->is_approved)
                        <span class="badge bg-warning text-dark">Jóváhagyásra vár</span>
                    @else
                        <span class="badge bg-success">Jóváhagyott</span>
                    @endif
                </div>
                <div class="card-body">
                    <!-- Book Information -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            @if($review->book->cover_image)
                                <img src="{{ asset('storage/' . $review->book->cover_image) }}" 
                                     alt="{{ $review->book->title }}" class="img-fluid rounded">
                            @else
                                <div class="bg-light rounded d-flex align-items-center justify-content-center" 
                                     style="height: 200px;">
                                    <i class="fas fa-book fa-3x text-muted"></i>
                                </div>
                            @endif
                        </div>
                        <div class="col-md-9">
                            <h5>
                                <a href="{{ route('books.show', $review->book) }}" class="text-decoration-none">
                                    {{ $review->book->title }}
                                </a>
                            </h5>
                            <p class="text-muted mb-2">
                                <i class="fas fa-user"></i> Szerző: {{ $review->book->author->name }}
                            </p>
                            <p class="text-muted mb-2">
                                <i class="fas fa-tag"></i> Kategória: {{ $review->book->category->name }}
                            </p>
                            @if($review->book->isbn)
                                <p class="text-muted mb-2">
                                    <i class="fas fa-barcode"></i> ISBN: {{ $review->book->isbn }}
                                </p>
                            @endif
                        </div>
                    </div>

                    <hr>

                    <!-- Review Information -->
                    <div class="row">
                        <div class="col-md-6">
                            <h6><i class="fas fa-star text-warning"></i> Értékelés</h6>
                            <div class="mb-3">
                                <div class="text-warning fs-4">
                                    {!! $review->stars_display !!}
                                </div>
                                <span class="text-muted">{{ $review->rating }}/5 csillag</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6><i class="fas fa-user"></i> Értékelő</h6>
                            <p class="mb-3">{{ $review->user->name }}</p>
                        </div>
                    </div>

                    @if($review->comment)
                        <div class="mb-3">
                            <h6><i class="fas fa-comment"></i> Megjegyzés</h6>
                            <div class="bg-light p-3 rounded">
                                {{ $review->comment }}
                            </div>
                        </div>
                    @endif

                    <!-- Dates -->
                    <div class="row">
                        <div class="col-md-6">
                            <h6><i class="fas fa-calendar"></i> Létrehozva</h6>
                            <p class="mb-2">{{ $review->created_at->format('Y. m. d. H:i') }}</p>
                        </div>
                        @if($review->is_approved && $review->approved_at)
                            <div class="col-md-6">
                                <h6><i class="fas fa-check-circle text-success"></i> Jóváhagyva</h6>
                                <p class="mb-2">
                                    {{ $review->approved_at->format('Y. m. d. H:i') }}
                                    @if($review->approvedBy)
                                        <br><small class="text-muted">Jóváhagyta: {{ $review->approvedBy->name }}</small>
                                    @endif
                                </p>
                            </div>
                        @endif
                    </div>

                    @if($review->updated_at && $review->updated_at != $review->created_at)
                        <div class="mb-3">
                            <h6><i class="fas fa-edit"></i> Utoljára módosítva</h6>
                            <p class="mb-0">{{ $review->updated_at->format('Y. m. d. H:i') }}</p>
                        </div>
                    @endif

                    <!-- Action Buttons -->
                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <a href="{{ route('reviews.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Vissza a listához
                        </a>
                        
                        <div class="btn-group" role="group">
                            <a href="{{ route('books.show', $review->book) }}" class="btn btn-outline-primary">
                                <i class="fas fa-book"></i> Könyv megtekintése
                            </a>
                            
                            @if($review->canBeEditedBy(auth()->user()))
                                <a href="{{ route('reviews.edit', $review) }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-edit"></i> Szerkesztés
                                </a>
                            @endif
                            
                            @if((auth()->user()->is_admin || auth()->user()->is_librarian) && !$review->is_approved)
                                <form method="POST" action="{{ route('reviews.approve', $review) }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-success" 
                                            onclick="return confirm('Biztosan jóváhagyod ezt az értékelést?')">
                                        <i class="fas fa-check"></i> Jóváhagyás
                                    </button>
                                </form>
                            @endif
                            
                            @if($review->canBeEditedBy(auth()->user()) || auth()->user()->is_admin)
                                <form method="POST" action="{{ route('reviews.destroy', $review) }}" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger" 
                                            onclick="return confirm('Biztosan törölni szeretnéd ezt az értékelést?')">
                                        <i class="fas fa-trash"></i> Törlés
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
