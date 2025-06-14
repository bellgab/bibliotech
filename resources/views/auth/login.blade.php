@extends('layouts.app')

@section('title', 'Bejelentkezés')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">Bejelentkezés</h4>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">E-mail cím</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" 
                               id="email" name="email" value="{{ old('email') }}" required autofocus>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">Jelszó</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" 
                               id="password" name="password" required>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="remember" name="remember">
                        <label class="form-check-label" for="remember">Emlékezzen rám</label>
                    </div>
                    
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Bejelentkezés</button>
                    </div>
                </form>
                
                <div class="text-center mt-3">
                    <p>Még nincs fiókja? <a href="{{ route('register') }}">Regisztráljon itt</a></p>
                </div>
            </div>
        </div>
        
        <div class="mt-4">
            <div class="alert alert-info">
                <h6>Teszt felhasználók:</h6>
                <ul class="mb-0">
                    <li><strong>Admin:</strong> admin@bibliotech.hu / password</li>
                    <li><strong>Könyvtáros:</strong> librarian@bibliotech.hu / password</li>
                    <li><strong>Tag:</strong> kiss.janos@email.hu / password</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
