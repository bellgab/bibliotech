// BiblioTech JavaScript Functions

document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Auto-hide alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert:not(.alert-permanent)');
    alerts.forEach(function(alert) {
        setTimeout(function() {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 5000);
    });

    // Confirm delete actions
    const deleteButtons = document.querySelectorAll('.btn-delete');
    deleteButtons.forEach(function(button) {
        button.addEventListener('click', function(e) {
            if (!confirm('Biztosan törölni szeretné ezt az elemet?')) {
                e.preventDefault();
            }
        });
    });

    // Search functionality
    const searchInput = document.getElementById('search');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const rows = document.querySelectorAll('tbody tr');
            
            rows.forEach(function(row) {
                const text = row.textContent.toLowerCase();
                if (text.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }

    // Date picker initialization
    const datePickers = document.querySelectorAll('input[type="date"]');
    datePickers.forEach(function(picker) {
        if (!picker.value) {
            picker.value = new Date().toISOString().split('T')[0];
        }
    });
});

// AJAX form submission helper
function submitForm(formId, successCallback) {
    const form = document.getElementById(formId);
    const formData = new FormData(form);
    
    fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            successCallback(data);
        } else {
            alert('Hiba történt: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Hiba történt a kérés feldolgozása során.');
    });
}

// Format currency
function formatCurrency(amount) {
    return new Intl.NumberFormat('hu-HU', {
        style: 'currency',
        currency: 'HUF'
    }).format(amount);
}

// Format date
function formatDate(dateString) {
    return new Date(dateString).toLocaleDateString('hu-HU');
}
