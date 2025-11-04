// CSRF Protection Enhancement
document.addEventListener('DOMContentLoaded', function() {
    // Get CSRF token from meta tag
    const getCSRFToken = () => {
        const metaToken = document.querySelector('meta[name="csrf-token"]');
        return metaToken ? metaToken.getAttribute('content') : null;
    };

    // Refresh CSRF token from server
    const refreshCSRFToken = async () => {
        try {
            const response = await fetch('/debug/csrf', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });
            
            if (response.ok) {
                const data = await response.json();
                
                // Update meta tag
                const metaToken = document.querySelector('meta[name="csrf-token"]');
                if (metaToken) {
                    metaToken.setAttribute('content', data.csrf_token);
                }
                
                // Update all CSRF token inputs
                document.querySelectorAll('input[name="_token"]').forEach(input => {
                    input.value = data.csrf_token;
                });
                
                console.log('CSRF token refreshed successfully');
                return data.csrf_token;
            }
        } catch (error) {
            console.error('Failed to refresh CSRF token:', error);
        }
        return null;
    };

    // Handle CSRF errors in AJAX responses
    const handleCSRFError = async (response) => {
        if (response.status === 419) {
            try {
                const errorData = await response.json();
                if (errorData.csrf_token) {
                    // Update CSRF token from error response
                    const metaToken = document.querySelector('meta[name="csrf-token"]');
                    if (metaToken) {
                        metaToken.setAttribute('content', errorData.csrf_token);
                    }
                    
                    document.querySelectorAll('input[name="_token"]').forEach(input => {
                        input.value = errorData.csrf_token;
                    });
                    
                    return errorData.csrf_token;
                }
            } catch (e) {
                // Fallback to refreshing token
                return await refreshCSRFToken();
            }
        }
        return null;
    };

    // Auto-refresh CSRF token every 30 minutes
    setInterval(refreshCSRFToken, 30 * 60 * 1000);

    // Handle form submissions with CSRF error retry
    document.addEventListener('submit', async function(e) {
        const form = e.target;
        
        // Only handle forms with CSRF tokens
        const csrfInput = form.querySelector('input[name="_token"]');
        if (!csrfInput) return;

        // Add a retry mechanism for 419 errors
        form.addEventListener('error', async function(errorEvent) {
            if (errorEvent.detail && errorEvent.detail.status === 419) {
                console.log('CSRF error detected, attempting to refresh token...');
                
                const newToken = await refreshCSRFToken();
                if (newToken) {
                    // Update form token and retry
                    csrfInput.value = newToken;
                    console.log('Token refreshed, retrying form submission...');
                    
                    // Prevent infinite retry loop
                    if (!form.dataset.csrfRetried) {
                        form.dataset.csrfRetried = 'true';
                        form.submit();
                    }
                }
            }
        });
    });

    // Session activity tracker to extend session lifetime
    let lastActivity = Date.now();
    const ACTIVITY_THRESHOLD = 5 * 60 * 1000; // 5 minutes
    const SESSION_EXTEND_INTERVAL = 15 * 60 * 1000; // 15 minutes

    const trackActivity = () => {
        lastActivity = Date.now();
    };

    // Track user activity
    ['mousedown', 'mousemove', 'keypress', 'scroll', 'touchstart', 'click'].forEach(event => {
        document.addEventListener(event, trackActivity, { passive: true });
    });

    // Extend session if user is active
    const extendSession = async () => {
        if (Date.now() - lastActivity < ACTIVITY_THRESHOLD) {
            try {
                await fetch('/debug/csrf', {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                console.log('Session extended due to user activity');
            } catch (error) {
                console.error('Failed to extend session:', error);
            }
        }
    };

    // Extend session every 15 minutes if user is active
    setInterval(extendSession, SESSION_EXTEND_INTERVAL);

    // Warning for page unload with unsaved forms
    window.addEventListener('beforeunload', function(e) {
        const forms = document.querySelectorAll('form');
        let hasUnsavedChanges = false;

        forms.forEach(form => {
            const inputs = form.querySelectorAll('input, textarea, select');
            inputs.forEach(input => {
                if (input.type !== 'hidden' && input.value !== input.defaultValue) {
                    hasUnsavedChanges = true;
                }
            });
        });

        if (hasUnsavedChanges) {
            e.preventDefault();
            e.returnValue = 'You have unsaved changes. Are you sure you want to leave?';
        }
    });

    console.log('CSRF protection enhancement loaded');
});
