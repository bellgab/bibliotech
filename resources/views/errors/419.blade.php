@extends('layouts.app')

@section('title', 'Session Expired')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card border-warning">
            <div class="card-header bg-warning text-dark">
                <h4 class="mb-0">
                    <i class="bi bi-exclamation-triangle"></i> Your Session Has Expired
                </h4>
            </div>
            <div class="card-body">
                <div class="alert alert-warning">
                    <h5>What happened?</h5>
                    <p class="mb-1">Your session has expired for security reasons. This can happen when:</p>
                    <ul>
                        <li>You've been inactive for too long</li>
                        <li>You have multiple browser tabs open</li>
                        <li>Your browser cached an old form</li>
                    </ul>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="d-grid gap-2">
                            <button onclick="history.back()" class="btn btn-primary">
                                <i class="bi bi-arrow-left"></i> Go Back & Retry
                            </button>
                            <a href="{{ route('dashboard') }}" class="btn btn-outline-primary">
                                <i class="bi bi-house"></i> Go to Dashboard
                            </a>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6>Quick Tips:</h6>
                                <ul class="small mb-0">
                                    <li>Use the back button to retry your action</li>
                                    <li>Refresh the page if needed</li>
                                    <li>Avoid keeping forms open for long periods</li>
                                    <li>Try to work in a single browser tab</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                
                @if(session('csrf'))
                    <div class="alert alert-info mt-3">
                        <i class="bi bi-info-circle"></i> {{ session('csrf') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
// Auto-refresh CSRF token on this page
document.addEventListener('DOMContentLoaded', function() {
    // Try to refresh CSRF token
    setTimeout(function() {
        const metaToken = document.querySelector('meta[name="csrf-token"]');
        if (metaToken) {
            fetch('/debug/csrf', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.csrf_token) {
                    metaToken.setAttribute('content', data.csrf_token);
                    console.log('CSRF token refreshed on error page');
                }
            })
            .catch(error => console.log('Could not refresh CSRF token:', error));
        }
    }, 1000);
});
</script>
@endsection
