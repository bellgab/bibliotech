@extends('layouts.app')

@section('title', $user->name)

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0">{{ $user->name }}</h4>
                <div>
                    @can('update', $user)
                        <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-pencil"></i> Szerkesztés
                        </a>
                    @endcan
                    @can('delete', $user)
                        @if($user->id !== auth()->id())
                            <form method="POST" action="{{ route('users.destroy', $user) }}" 
                                  class="d-inline" onsubmit="return confirm('Biztosan törli ezt a felhasználót?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                    <i class="bi bi-trash"></i> Törlés
                                </button>
                            </form>
                        @endif
                    @endcan
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th width="30%">E-mail:</th>
                                <td>
                                    {{ $user->email }}
                                    @if($user->email_verified_at)
                                        <i class="bi bi-check-circle-fill text-success ms-1" 
                                           title="Ellenőrzött e-mail"></i>
                                    @else
                                        <i class="bi bi-exclamation-circle-fill text-warning ms-1" 
                                           title="Nem ellenőrzött e-mail"></i>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Tagsági szám:</th>
                                <td><code>{{ $user->membership_number }}</code></td>
                            </tr>
                            <tr>
                                <th>Szerepkör:</th>
                                <td>
                                    @switch($user->role)
                                        @case('admin')
                                            <span class="badge bg-danger">Adminisztrátor</span>
                                            @break
                                        @case('librarian')
                                            <span class="badge bg-warning">Könyvtáros</span>
                                            @break
                                        @case('member')
                                            <span class="badge bg-primary">Tag</span>
                                            @break
                                        @default
                                            <span class="badge bg-secondary">{{ ucfirst($user->role) }}</span>
                                    @endswitch
                                </td>
                            </tr>
                            <tr>
                                <th>Telefonszám:</th>
                                <td>{{ $user->phone ?? 'Nincs megadva' }}</td>
                            </tr>
                            <tr>
                                <th>Regisztráció:</th>
                                <td>{{ $user->created_at->format('Y. m. d.') }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        @if($user->address)
                            <h6>Cím:</h6>
                            <p>{{ $user->address }}</p>
                        @endif
                        
                        @php
                            $activeBorrows = $user->borrows()->whereNull('returned_at')->count();
                            $totalBorrows = $user->borrows()->count();
                            $overdueBorrows = $user->borrows()->whereNull('returned_at')
                                ->where('due_date', '<', now())->count();
                        @endphp
                        
                        <h6>Kölcsönzési statisztikák:</h6>
                        <ul class="list-unstyled">
                            <li><strong>Aktív kölcsönzések:</strong> {{ $activeBorrows }} db</li>
                            <li><strong>Összes kölcsönzés:</strong> {{ $totalBorrows }} db</li>
                            @if($overdueBorrows > 0)
                                <li><strong>Lejárt kölcsönzések:</strong> 
                                    <span class="text-danger">{{ $overdueBorrows }} db</span>
                                </li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <!-- Quick actions -->
        <div class="card mb-3">
            <div class="card-header">
                <h6 class="mb-0">Műveletek</h6>
            </div>
            <div class="card-body">
                @can('create', App\Models\BookBorrowing::class)
                    <a href="{{ route('borrows.create', ['user_id' => $user->id]) }}" 
                       class="btn btn-success w-100 mb-2">
                        <i class="bi bi-book"></i> Új kölcsönzés
                    </a>
                @endcan
                
                <a href="{{ route('users.index') }}" class="btn btn-outline-secondary w-100">
                    <i class="bi bi-arrow-left"></i> Vissza a listához
                </a>
            </div>
        </div>
        
        <!-- Status card -->
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Állapot</h6>
            </div>
            <div class="card-body text-center">
                @if($overdueBorrows > 0)
                    <i class="bi bi-exclamation-triangle-fill text-danger display-4"></i>
                    <h6 class="text-danger mt-2">Lejárt kölcsönzés</h6>
                @elseif($activeBorrows > 0)
                    <i class="bi bi-clock-fill text-warning display-4"></i>
                    <h6 class="text-warning mt-2">Aktív kölcsönzés</h6>
                @else
                    <i class="bi bi-check-circle-fill text-success display-4"></i>
                    <h6 class="text-success mt-2">Rendben</h6>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Borrowing history -->
@if($user->borrows()->count() > 0)
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Kölcsönzési előzmények</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Könyv</th>
                                    <th>Kölcsönzés</th>
                                    <th>Lejárat</th>
                                    <th>Visszahozás</th>
                                    <th>Állapot</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($user->borrows()->with('book')->latest()->limit(10)->get() as $borrow)
                                    <tr class="{{ $borrow->is_overdue && !$borrow->returned_at ? 'table-warning' : '' }}">
                                        <td>
                                            <a href="{{ route('books.show', $borrow->book) }}">
                                                {{ $borrow->book->title }}
                                            </a>
                                        </td>
                                        <td>{{ $borrow->borrowed_at->format('Y.m.d') }}</td>
                                        <td>{{ $borrow->due_date->format('Y.m.d') }}</td>
                                        <td>
                                            @if($borrow->returned_at)
                                                {{ $borrow->returned_at->format('Y.m.d') }}
                                            @else
                                                <em class="text-muted">-</em>
                                            @endif
                                        </td>
                                        <td>
                                            @if($borrow->returned_at)
                                                <span class="badge bg-success">Visszahozva</span>
                                            @elseif($borrow->is_overdue)
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
                    
                    @if($user->borrows()->count() > 10)
                        <div class="text-center mt-2">
                            <a href="{{ route('borrows.index', ['user_id' => $user->id]) }}" 
                               class="btn btn-sm btn-outline-primary">
                                Összes kölcsönzés megtekintése
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endif
@endsection
