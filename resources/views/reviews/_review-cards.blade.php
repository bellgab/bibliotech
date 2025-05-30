@if($reviews->count() > 0)
    <div class="row">
        @foreach($reviews as $review)
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100 {{ !$review->is_approved ? 'border-warning' : '' }}">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h6 class="card-title mb-0">
                                <a href="{{ route('books.show', $review->book) }}" class="text-decoration-none">
                                    {{ Str::limit($review->book->title, 30) }}
                                </a>
                            </h6>
                            @if(!$review->is_approved)
                                <span class="badge bg-warning text-dark">Várakozik</span>
                            @else
                                <span class="badge bg-success">Jóváhagyott</span>
                            @endif
                        </div>
                        
                        <p class="text-muted small mb-2">
                            <i class="fas fa-user"></i> {{ $review->user->name }}
                            <br>
                            <i class="fas fa-book"></i> {{ $review->book->author->name }}
                        </p>
                        
                        <div class="mb-2">
                            <div class="text-warning">
                                {!! $review->stars_display !!}
                            </div>
                        </div>
                        
                        @if($review->comment)
                            <p class="card-text">
                                {{ Str::limit($review->comment, 100) }}
                            </p>
                        @endif
                        
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">
                                {{ $review->created_at->format('Y.m.d H:i') }}
                            </small>
                            
                            <div class="btn-group" role="group">
                                <a href="{{ route('reviews.show', $review) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye"></i>
                                </a>
                                
                                @if($review->canBeEditedBy(auth()->user()))
                                    <a href="{{ route('reviews.edit', $review) }}" class="btn btn-sm btn-outline-secondary">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                @endif
                                
                                @if(auth()->user()->is_admin || auth()->user()->is_librarian)
                                    @if(!$review->is_approved)
                                        <form method="POST" action="{{ route('reviews.approve', $review) }}" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success" 
                                                    onclick="return confirm('Biztosan jóváhagyod ezt az értékelést?')">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                    @endif
                                @endif
                                
                                @if($review->canBeEditedBy(auth()->user()) || auth()->user()->is_admin)
                                    <form method="POST" action="{{ route('reviews.destroy', $review) }}" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                onclick="return confirm('Biztosan törölni szeretnéd ezt az értékelést?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-center">
        {{ $reviews->withQueryString()->links() }}
    </div>
@else
    <div class="text-center py-5">
        <i class="fas fa-star fa-3x text-muted mb-3"></i>
        <h4>Nincs értékelés</h4>
        <p class="text-muted">
            @if(request()->hasAny(['search', 'status', 'book_id']))
                Nincs a keresési feltételeknek megfelelő értékelés.
            @else
                Még nincs értékelés a rendszerben.
            @endif
        </p>
        @if(!request()->hasAny(['search', 'status', 'book_id']))
            <a href="{{ route('reviews.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Első értékelés létrehozása
            </a>
        @endif
    </div>
@endif
