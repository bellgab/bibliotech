@extends('layouts.app')

@section('title', 'Irányítópult')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Irányítópult</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <button type="button" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-download"></i> Export
            </button>
        </div>
    </div>
</div>

@auth
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card stats-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="card-title text-muted">Összes könyv</h5>
                            <h2 class="text-primary">{{ $totalBooks ?? 0 }}</h2>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-book fs-1 text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card stats-card success">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="card-title text-muted">Elérhető könyvek</h5>
                            <h2 class="text-success">{{ $availableBooks ?? 0 }}</h2>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-check-circle fs-1 text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card stats-card warning">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="card-title text-muted">Kölcsönzött könyvek</h5>
                            <h2 class="text-warning">{{ $borrowedBooks ?? 0 }}</h2>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-arrow-repeat fs-1 text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card stats-card danger">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="card-title text-muted">Lejárt kölcsönzések</h5>
                            <h2 class="text-danger">{{ $overdueBooks ?? 0 }}</h2>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-exclamation-triangle fs-1 text-danger"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Legújabb kölcsönzések</h5>
                </div>
                <div class="card-body">
                    @if(isset($recentBorrows) && $recentBorrows->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Könyv</th>
                                        <th>Kölcsönző</th>
                                        <th>Kölcsönzés dátuma</th>
                                        <th>Visszahozási határidő</th>
                                        <th>Státusz</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentBorrows as $borrow)
                                        <tr>
                                            <td>{{ $borrow->book->title }}</td>
                                            <td>{{ $borrow->user->name }}</td>
                                            <td>{{ $borrow->borrowed_at->format('Y.m.d') }}</td>
                                            <td>{{ $borrow->due_date->format('Y.m.d') }}</td>
                                            <td>
                                                @if($borrow->returned_at)
                                                    <span class="badge bg-success">Visszahozva</span>
                                                @elseif($borrow->due_date < now())
                                                    <span class="badge bg-danger">Lejárt</span>
                                                @else
                                                    <span class="badge bg-warning">Kölcsönzött</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted">Nincsenek kölcsönzések.</p>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Népszerű könyvek</h5>
                </div>
                <div class="card-body">
                    @if(isset($popularBooks) && $popularBooks->count() > 0)
                        @foreach($popularBooks as $book)
                            <div class="d-flex align-items-center mb-3">
                                <div class="flex-shrink-0">
                                    <div class="book-cover bg-light d-flex align-items-center justify-content-center">
                                        <i class="bi bi-book text-muted"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-0">{{ $book->title }}</h6>
                                    <small class="text-muted">{{ $book->author->name }}</small>
                                    <br>
                                    <small class="text-info">{{ $book->borrows_count ?? 0 }} kölcsönzés</small>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <p class="text-muted">Nincsenek adatok.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@else
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body text-center">
                    <h1 class="card-title">Üdvözöljük a BiblioTech rendszerben!</h1>
                    <p class="card-text lead">Modern könyvtár kezelő rendszer könyvek, felhasználók és kölcsönzések nyilvántartására.</p>
                    <div class="row mt-4">
                        <div class="col-md-4">
                            <i class="bi bi-book fs-1 text-primary mb-3"></i>
                            <h5>Könyvek kezelése</h5>
                            <p>Könyvek hozzáadása, szerkesztése és törlése egyszerűen.</p>
                        </div>
                        <div class="col-md-4">
                            <i class="bi bi-people fs-1 text-success mb-3"></i>
                            <h5>Felhasználók</h5>
                            <p>Tagok regisztrációja és adatainak kezelése.</p>
                        </div>
                        <div class="col-md-4">
                            <i class="bi bi-arrow-repeat fs-1 text-warning mb-3"></i>
                            <h5>Kölcsönzések</h5>
                            <p>Kölcsönzések nyilvántartása és visszahozás kezelése.</p>
                        </div>
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('login') }}" class="btn btn-primary btn-lg me-3">Bejelentkezés</a>
                        <a href="{{ route('register') }}" class="btn btn-outline-primary btn-lg">Regisztráció</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endauth

@endsection
