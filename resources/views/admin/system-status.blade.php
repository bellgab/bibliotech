@extends('layouts.app')

@section('title', 'System Status')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h3 mb-0">System Status & Diagnostics</h2>
            <p class="text-muted">Comprehensive system health monitoring</p>
        </div>
        <div>
            <button class="btn btn-outline-primary" onclick="refreshStatus()">
                <i class="fas fa-sync"></i> Refresh
            </button>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>
    </div>

    <!-- System Health Status -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-heartbeat"></i> System Health Checks
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($systemHealth as $component => $status)
                        <div class="col-md-2 col-sm-4 col-6 mb-3">
                            <div class="text-center p-3 border rounded {{ $status ? 'bg-light-success' : 'bg-light-danger' }}">
                                <div class="mb-2">
                                    @if($status)
                                        <i class="fas fa-check-circle text-success fa-2x"></i>
                                    @else
                                        <i class="fas fa-times-circle text-danger fa-2x"></i>
                                    @endif
                                </div>
                                <h6 class="mb-1">{{ ucfirst(str_replace('_', ' ', $component)) }}</h6>
                                <small class="text-muted">
                                    {{ $status ? 'Healthy' : 'Issue Detected' }}
                                </small>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Features Status -->
    <div class="row mb-4">
        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-info text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-cogs"></i> Feature Status
                    </h5>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        @foreach($features as $feature => $enabled)
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span>
                                <i class="fas fa-{{ $enabled ? 'check' : 'times' }} text-{{ $enabled ? 'success' : 'danger' }} me-2"></i>
                                {{ ucfirst(str_replace('_', ' ', $feature)) }}
                            </span>
                            <span class="badge bg-{{ $enabled ? 'success' : 'secondary' }}">
                                {{ $enabled ? 'Enabled' : 'Disabled' }}
                            </span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-success text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-line"></i> Recent Activity
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6 mb-3">
                            <div class="border rounded p-3">
                                <h4 class="text-primary mb-1">{{ $recentActivity['new_books_today'] }}</h4>
                                <small class="text-muted">New Books Today</small>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="border rounded p-3">
                                <h4 class="text-info mb-1">{{ $recentActivity['new_users_today'] }}</h4>
                                <small class="text-muted">New Users Today</small>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="border rounded p-3">
                                <h4 class="text-success mb-1">{{ $recentActivity['borrows_today'] }}</h4>
                                <small class="text-muted">Borrows Today</small>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="border rounded p-3">
                                <h4 class="text-secondary mb-1">{{ $recentActivity['returns_today'] }}</h4>
                                <small class="text-muted">Returns Today</small>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="border rounded p-3 {{ $recentActivity['overdue_books'] > 0 ? 'bg-light-warning' : '' }}">
                                <h4 class="text-{{ $recentActivity['overdue_books'] > 0 ? 'warning' : 'success' }} mb-1">
                                    {{ $recentActivity['overdue_books'] }}
                                </h4>
                                <small class="text-muted">Overdue Books</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Performance Metrics -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-warning text-dark">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-tachometer-alt"></i> Performance Metrics
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="text-center p-3 border rounded">
                                <i class="fas fa-database text-primary fa-2x mb-2"></i>
                                <h6>Database Size</h6>
                                <p class="mb-0 fw-bold">{{ $performance['database_size'] }}</p>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="text-center p-3 border rounded">
                                <i class="fas fa-memory text-info fa-2x mb-2"></i>
                                <h6>Memory Usage</h6>
                                <p class="mb-0 fw-bold">{{ $performance['memory_usage'] }}</p>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="text-center p-3 border rounded">
                                <i class="fas fa-hdd text-success fa-2x mb-2"></i>
                                <h6>Storage Size</h6>
                                <p class="mb-0 fw-bold">{{ $performance['storage_size'] }}</p>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="text-center p-3 border rounded">
                                <i class="fas fa-clock text-secondary fa-2x mb-2"></i>
                                <h6>Avg Response</h6>
                                <p class="mb-0 fw-bold">{{ $performance['average_response_time'] }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Configuration & Error Summary -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-dark text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-cog"></i> System Configuration
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <tbody>
                                @foreach($configuration as $key => $value)
                                <tr>
                                    <td><strong>{{ ucfirst(str_replace('_', ' ', $key)) }}</strong></td>
                                    <td>
                                        <code>{{ is_bool($value) ? ($value ? 'true' : 'false') : $value }}</code>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header bg-danger text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-exclamation-triangle"></i> Error Summary
                    </h5>
                </div>
                <div class="card-body text-center">
                    <div class="mb-3">
                        <h2 class="text-danger mb-1">{{ $errorSummary['recent'] }}</h2>
                        <small class="text-muted">Recent Errors (24h)</small>
                    </div>
                    <div class="mb-3">
                        <h4 class="text-secondary mb-1">{{ $errorSummary['total'] }}</h4>
                        <small class="text-muted">Total Errors in Log</small>
                    </div>
                    @if($errorSummary['recent'] > 0)
                        <div class="alert alert-warning alert-sm">
                            <i class="fas fa-exclamation-circle"></i>
                            Recent errors detected. Check logs for details.
                        </div>
                    @else
                        <div class="alert alert-success alert-sm">
                            <i class="fas fa-check-circle"></i>
                            No recent errors detected.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- API Endpoint for Monitoring -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-code"></i> Monitoring API
                    </h5>
                </div>
                <div class="card-body">
                    <p class="mb-2">Use this endpoint for external monitoring systems:</p>
                    <div class="input-group">
                        <input type="text" class="form-control" value="{{ route('system.diagnostics') }}" readonly id="apiEndpoint">
                        <button class="btn btn-outline-secondary" type="button" onclick="copyToClipboard('apiEndpoint')">
                            <i class="fas fa-copy"></i> Copy
                        </button>
                    </div>
                    <small class="text-muted">Returns JSON status data for automated monitoring</small>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function refreshStatus() {
    // Simple page reload for now - could be enhanced with AJAX
    window.location.reload();
}

function copyToClipboard(elementId) {
    const element = document.getElementById(elementId);
    element.select();
    element.setSelectionRange(0, 99999);
    navigator.clipboard.writeText(element.value);
    
    // Show feedback
    showToast('API endpoint copied to clipboard!', 'success');
}

function showToast(message, type = 'info') {
    const toastHtml = `
        <div class="toast position-fixed top-0 end-0 m-3" role="alert">
            <div class="toast-body bg-${type === 'success' ? 'success' : 'info'} text-white">
                <i class="fas fa-${type === 'success' ? 'check-circle' : 'info-circle'}"></i>
                ${message}
            </div>
        </div>
    `;
    
    const toastElement = document.createElement('div');
    toastElement.innerHTML = toastHtml;
    document.body.appendChild(toastElement.firstElementChild);
    
    const toast = new bootstrap.Toast(toastElement.firstElementChild);
    toast.show();
    
    setTimeout(() => {
        toastElement.firstElementChild.remove();
    }, 3000);
}

// Auto-refresh every 5 minutes
setTimeout(function() {
    window.location.reload();
}, 300000);
</script>

<style>
.bg-light-success {
    background-color: rgba(40, 167, 69, 0.1) !important;
}

.bg-light-danger {
    background-color: rgba(220, 53, 69, 0.1) !important;
}

.bg-light-warning {
    background-color: rgba(255, 193, 7, 0.1) !important;
}

.card {
    border: none;
    transition: transform 0.2s;
}

.card:hover {
    transform: translateY(-2px);
}

.alert-sm {
    padding: 0.5rem;
    margin-bottom: 0;
}

code {
    background-color: #f8f9fa;
    padding: 2px 4px;
    border-radius: 3px;
    font-size: 0.875em;
}
</style>
@endsection
