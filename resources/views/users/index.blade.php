@extends('layouts.app')

@section('title', 'Felhasználók')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Felhasználók</h1>
    @can('create', App\Models\User::class)
        <a href="{{ route('users.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Új felhasználó
        </a>
    @endcan
</div>

@if($users->count() > 0)
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Név</th>
                            <th>E-mail</th>
                            <th>Tagsági szám</th>
                            <th>Szerepkör</th>
                            <th>Telefonszám</th>
                            <th>Aktív kölcsönzések</th>
                            <th>Műveletek</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                            <tr>
                                <td>
                                    <strong>{{ $user->name }}</strong>
                                    @if($user->email_verified_at)
                                        <i class="bi bi-check-circle-fill text-success" 
                                           title="Ellenőrzött e-mail"></i>
                                    @else
                                        <i class="bi bi-exclamation-circle-fill text-warning" 
                                           title="Nem ellenőrzött e-mail"></i>
                                    @endif
                                </td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    <code>{{ $user->membership_number }}</code>
                                </td>
                                <td>
                                    @switch($user->role)
                                        @case('admin')
                                            <span class="badge bg-danger">Admin</span>
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
                                <td>{{ $user->phone ?? '-' }}</td>
                                <td>
                                    @php
                                        $activeBorrows = $user->borrows()->whereNull('returned_at')->count();
                                    @endphp
                                    @if($activeBorrows > 0)
                                        <span class="badge bg-info">{{ $activeBorrows }} db</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('users.show', $user) }}" 
                                           class="btn btn-outline-primary">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        @can('update', $user)
                                            <a href="{{ route('users.edit', $user) }}" 
                                               class="btn btn-outline-secondary">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                        @endcan
                                        @can('delete', $user)
                                            @if($user->id !== auth()->id())
                                                <form method="POST" action="{{ route('users.destroy', $user) }}" 
                                                      class="d-inline" onsubmit="return confirm('Biztosan törli ezt a felhasználót?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-outline-danger">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Pagination -->
    <div class="d-flex justify-content-center mt-3">
        {{ $users->links() }}
    </div>
@else
    <div class="text-center py-5">
        <i class="bi bi-people display-1 text-muted"></i>
        <h3 class="text-muted mt-3">Nincs felhasználó</h3>
        <p class="text-muted">Még nem adtak hozzá felhasználót a rendszerhez.</p>
        @can('create', App\Models\User::class)
            <a href="{{ route('users.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Első felhasználó hozzáadása
            </a>
        @endcan
    </div>
@endif

<!-- Statistics Cards -->
<div class="row mt-4">
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="card-title">{{ $users->where('role', 'admin')->count() }}</h5>
                <p class="card-text text-muted">Adminisztrátor</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="card-title">{{ $users->where('role', 'librarian')->count() }}</h5>
                <p class="card-text text-muted">Könyvtáros</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="card-title">{{ $users->where('role', 'member')->count() }}</h5>
                <p class="card-text text-muted">Tag</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="card-title">{{ $users->whereNotNull('email_verified_at')->count() }}</h5>
                <p class="card-text text-muted">Ellenőrzött e-mail</p>
            </div>
        </div>
    </div>
</div>
@endsection
