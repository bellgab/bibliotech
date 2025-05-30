@extends('layouts.app')

@section('title', 'Könyv értékelések')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">Könyv értékelések</h1>
                <a href="{{ route('reviews.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Új értékelés
                </a>
            </div>

            <!-- Filters -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('reviews.index') }}" class="row g-3">
                        <div class="col-md-4">
                            <label for="search" class="form-label">Keresés</label>
                            <input type="text" class="form-control" id="search" name="search" 
                                   value="{{ request('search') }}" placeholder="Könyv cím, szerző, felhasználó...">
                        </div>
                        <div class="col-md-3">
                            <label for="status" class="form-label">Státusz</label>
                            <select class="form-select" id="status" name="status">
                                <option value="">Összes</option>
                                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Jóváhagyott</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Jóváhagyásra vár</option>
                            </select>
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-outline-primary me-2">
                                <i class="fas fa-search"></i> Keresés
                            </button>
                            <a href="{{ route('reviews.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times"></i> Törlés
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            @include('reviews._review-cards', ['reviews' => $reviews])
        </div>
    </div>
</div>
@endsection
