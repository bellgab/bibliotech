@extends('layouts.app')

@section('title', 'Kölcsönzés részletei')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Kölcsönzés részletei</h4>
                <div>
                    @if(!$borrow->returned_at)
                        @can('update', $borrow)
                            <form method="POST" action="{{ route('borrows.return', $borrow) }}" 
                                  class="d-inline" onsubmit="return confirm('Biztosan visszahozza a könyvet?')">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-success">
                                    <i class="bi bi-check"></i> Visszahozás
                                </button>
                            </form>
                        @endcan
                    @endif
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Könyv adatai</h6>
                        <table class="table table-borderless">
                            <tr>
                                <th width="30%">Cím:</th>
                                <td>
                                    <a href="{{ route('books.show', $borrow->book) }}">
                                        {{ $borrow->book->title }}
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <th>Szerző:</th>
                                <td>{{ $borrow->book->author->name }}</td>
                            </tr>
                            <tr>
                                <th>ISBN:</th>
                                <td>{{ $borrow->book->isbn }}</td>
                            </tr>
                            <tr>
                                <th>Kategória:</th>
                                <td>{{ $borrow->book->category->name }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6>Kölcsönző adatai</h6>
                        <table class="table table-borderless">
                            <tr>
                                <th width="30%">Név:</th>
                                <td>
                                    <a href="{{ route('users.show', $borrow->user) }}">
                                        {{ $borrow->user->name }}
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <th>E-mail:</th>
                                <td>{{ $borrow->user->email }}</td>
                            </tr>
                            <tr>
                                <th>Telefonszám:</th>
                                <td>{{ $borrow->user->phone ?? 'Nincs megadva' }}</td>
                            </tr>
                            <tr>
                                <th>Tagsági szám:</th>
                                <td>{{ $borrow->user->membership_number }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
                
                <hr>
                
                <div class="row">
                    <div class="col-md-12">
                        <h6>Kölcsönzés adatai</h6>
                        <table class="table table-borderless">
                            <tr>
                                <th width="20%">Kölcsönzés dátuma:</th>
                                <td>{{ $borrow->borrowed_at->format('Y. m. d. H:i') }}</td>
                            </tr>
                            <tr>
                                <th>Lejárat:</th>
                                <td>
                                    {{ $borrow->due_date->format('Y. m. d.') }}
                                    @if($borrow->is_overdue && !$borrow->returned_at)
                                        <span class="badge bg-danger ms-2">
                                            {{ $borrow->days_overdue }} nappal lejárt
                                        </span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Visszahozás:</th>
                                <td>
                                    @if($borrow->returned_at)
                                        {{ $borrow->returned_at->format('Y. m. d. H:i') }}
                                        <span class="badge bg-success ms-2">Visszahozva</span>
                                    @else
                                        <em class="text-muted">Még nem hozták vissza</em>
                                        @if($borrow->is_overdue)
                                            <span class="badge bg-danger ms-2">Lejárt</span>
                                        @else
                                            <span class="badge bg-warning ms-2">Kölcsönzött</span>
                                        @endif
                                    @endif
                                </td>
                            </tr>
                            @if($borrow->notes)
                            <tr>
                                <th>Megjegyzések:</th>
                                <td>{{ $borrow->notes }}</td>
                            </tr>
                            @endif
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <!-- Status card -->
        <div class="card mb-3">
            <div class="card-header">
                <h6 class="mb-0">Állapot</h6>
            </div>
            <div class="card-body text-center">
                @if($borrow->returned_at)
                    <i class="bi bi-check-circle-fill text-success display-4"></i>
                    <h5 class="text-success mt-2">Visszahozva</h5>
                    <p class="text-muted">{{ $borrow->returned_at->format('Y. m. d.') }}</p>
                @elseif($borrow->is_overdue)
                    <i class="bi bi-exclamation-triangle-fill text-danger display-4"></i>
                    <h5 class="text-danger mt-2">Lejárt</h5>
                    <p class="text-muted">{{ $borrow->days_overdue }} napja</p>
                @else
                    <i class="bi bi-clock-fill text-warning display-4"></i>
                    <h5 class="text-warning mt-2">Kölcsönzött</h5>
                    <p class="text-muted">
                        Lejárat: {{ $borrow->due_date->diffForHumans() }}
                    </p>
                @endif
            </div>
        </div>
        
        <!-- Action buttons -->
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Műveletek</h6>
            </div>
            <div class="card-body">
                <a href="{{ route('borrows.index') }}" class="btn btn-outline-secondary w-100 mb-2">
                    <i class="bi bi-arrow-left"></i> Vissza a listához
                </a>
                
                <a href="{{ route('books.show', $borrow->book) }}" class="btn btn-outline-primary w-100 mb-2">
                    <i class="bi bi-book"></i> Könyv megtekintése
                </a>
                
                <a href="{{ route('users.show', $borrow->user) }}" class="btn btn-outline-info w-100">
                    <i class="bi bi-person"></i> Felhasználó megtekintése
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
