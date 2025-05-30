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
                            @if($book->reviews_count > 0)
                            <tr>
                                <th>Értékelés:</th>
                                <td>
                                    <div class="text-warning">
                                        {!! $book->stars_display !!}
                                    </div>
                                    <small class="text-muted">
                                        {{ number_format($book->average_rating, 1) }}/5 
                                        ({{ $book->reviews_count }} értékelés)
                                    </small>
                                </td>
                            </tr>
                            @endif
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

        <!-- Reviews Section -->
        <div class="card mt-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Értékelések</h5>
                @auth
                    @php
                        $userReview = $book->reviews()->where('user_id', auth()->id())->first();
                    @endphp
                    @if(!$userReview)
                        <a href="{{ route('reviews.create', ['book_id' => $book->id]) }}" 
                           class="btn btn-sm btn-primary">
                            <i class="fas fa-star"></i> Értékelés írása
                        </a>
                    @endif
                @endauth
            </div>
            <div class="card-body">
                @php
                    $approvedReviews = $book->approvedReviews()->with('user')->latest()->get();
                @endphp
                
                @if($approvedReviews->count() > 0)
                    @foreach($approvedReviews as $review)
                        <div class="review-item {{ !$loop->last ? 'border-bottom' : '' }} pb-3 mb-3">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <div class="d-flex align-items-center mb-2">
                                        <strong class="me-3">{{ $review->user->name }}</strong>
                                        <div class="text-warning me-2">
                                            {!! $review->stars_display !!}
                                        </div>
                                        <small class="text-muted">
                                            {{ $review->created_at->format('Y.m.d') }}
                                        </small>
                                    </div>
                                    @if($review->comment)
                                        <p class="mb-0">{{ $review->comment }}</p>
                                    @endif
                                </div>
                                @if($review->canBeEditedBy(auth()->user()))
                                    <div class="ms-3">
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" 
                                                    type="button" data-bs-toggle="dropdown">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('reviews.edit', $review) }}">
                                                        <i class="fas fa-edit"></i> Szerkesztés
                                                    </a>
                                                </li>
                                                <li>
                                                    <form method="POST" action="{{ route('reviews.destroy', $review) }}" 
                                                          class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dropdown-item text-danger"
                                                                onclick="return confirm('Biztosan törölni szeretnéd ezt az értékelést?')">
                                                            <i class="fas fa-trash"></i> Törlés
                                                        </button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                    
                    @if($approvedReviews->count() >= 5)
                        <div class="text-center">
                            <a href="{{ route('reviews.index', ['book_id' => $book->id]) }}" 
                               class="btn btn-outline-primary">
                                <i class="fas fa-eye"></i> Összes értékelés megtekintése
                            </a>
                        </div>
                    @endif
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-star fa-2x text-muted mb-3"></i>
                        <p class="text-muted mb-0">Ez a könyv még nem rendelkezik értékeléssel.</p>
                        @auth
                            @if(!$book->reviews()->where('user_id', auth()->id())->exists())
                                <p class="text-muted">Legyél te az első, aki értékeli!</p>
                            @endif
                        @endauth
                    </div>
                @endif
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
                
                <!-- QR Code Actions -->
                <div class="dropdown w-100 mb-2">
                    <button class="btn btn-outline-primary dropdown-toggle w-100" type="button" 
                            data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-qr-code"></i> QR Kód
                    </button>
                    <ul class="dropdown-menu w-100">
                        <li>
                            <a class="dropdown-item" href="{{ route('books.qr', $book) }}" target="_blank">
                                <i class="bi bi-eye"></i> QR kód megtekintése
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('qr.book.info', $book) }}" target="_blank">
                                <i class="bi bi-phone"></i> Mobil nézet
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item" href="#" onclick="copyQrUrl('{{ route('qr.book.info', $book) }}')">
                                <i class="bi bi-clipboard"></i> Link másolása
                            </a>
                        </li>
                    </ul>
                </div>
                
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

<script>
function copyQrUrl(url) {
    navigator.clipboard.writeText(url).then(function() {
        // Success feedback
        const toast = document.createElement('div');
        toast.className = 'toast position-fixed top-0 end-0 m-3';
        toast.innerHTML = `
            <div class="toast-body bg-success text-white">
                <i class="bi bi-check-circle"></i> QR link vágólapra másolva!
            </div>
        `;
        document.body.appendChild(toast);
        
        const bsToast = new bootstrap.Toast(toast);
        bsToast.show();
        
        setTimeout(() => {
            toast.remove();
        }, 3000);
    }).catch(function(err) {
        alert('Hiba történt a másolás során: ' + err);
    });
}
</script>
@endsection
