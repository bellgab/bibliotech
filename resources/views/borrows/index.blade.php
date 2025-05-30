@extends('layouts.app')

@section('title', 'Kölcsönzések')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Kölcsönzések</h1>
    @can('create', App\Models\BookBorrowing::class)
        <a href="{{ route('borrows.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Új kölcsönzés
        </a>
    @endcan
</div>

@if($borrows->count() > 0)
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Könyv</th>
                            <th>Kölcsönző</th>
                            <th>Kölcsönzés dátuma</th>
                            <th>Lejárat</th>
                            <th>Visszahozás</th>
                            <th>Állapot</th>
                            <th>Műveletek</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($borrows as $borrow)
                            <tr class="{{ $borrow->is_overdue ? 'table-danger' : '' }}">
                                <td>
                                    <strong>{{ $borrow->book->title }}</strong><br>
                                    <small class="text-muted">{{ $borrow->book->author->name }}</small>
                                </td>
                                <td>
                                    {{ $borrow->user->name }}<br>
                                    <small class="text-muted">{{ $borrow->user->email }}</small>
                                </td>
                                <td>{{ $borrow->borrowed_at->format('Y.m.d') }}</td>
                                <td>
                                    {{ $borrow->due_date->format('Y.m.d') }}
                                    @if($borrow->is_overdue && !$borrow->returned_at)
                                        <span class="badge bg-danger">Lejárt</span>
                                    @endif
                                </td>
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
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('borrows.show', $borrow) }}" 
                                           class="btn btn-outline-primary">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        @if(!$borrow->returned_at)
                                            @can('update', $borrow)
                                                <form method="POST" action="{{ route('borrows.return', $borrow) }}" 
                                                      class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-outline-success"
                                                            onclick="return confirm('Biztosan visszahozza a könyvet?')">
                                                        <i class="bi bi-check"></i>
                                                    </button>
                                                </form>
                                            @endcan
                                        @endif
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
        {{ $borrows->links() }}
    </div>
@else
    <div class="text-center py-5">
        <i class="bi bi-bookmark display-1 text-muted"></i>
        <h3 class="text-muted mt-3">Nincs kölcsönzés</h3>
        <p class="text-muted">Még nem történt kölcsönzés a rendszerben.</p>
        @can('create', App\Models\BookBorrowing::class)
            <a href="{{ route('borrows.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Első kölcsönzés
            </a>
        @endcan
    </div>
@endif
@endsection
