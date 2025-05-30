<?php $__env->startSection('title', 'Analytics Dashboard'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h3 mb-0">Analytics Dashboard</h2>
            <p class="text-muted">Detailed insights and statistics</p>
        </div>
        <div>
            <a href="<?php echo e(route('admin.dashboard')); ?>" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>
    </div>

    <!-- Monthly Trends Chart -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-line"></i> Monthly Activity Trends
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="monthlyTrendsChart" height="100"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Analytics Grid -->
    <div class="row">
        <!-- Category Distribution -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-info text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-pie"></i> Book Categories Distribution
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="categoryChart" height="300"></canvas>
                    
                    <!-- Category Stats Table -->
                    <div class="mt-4">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Category</th>
                                        <th class="text-end">Books</th>
                                        <th class="text-end">Total Copies</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $categoryStats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td><?php echo e($category->name); ?></td>
                                        <td class="text-end"><?php echo e($category->books_count); ?></td>
                                        <td class="text-end"><?php echo e($category->total_copies); ?></td>
                                    </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- User Activity Patterns -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-success text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-users"></i> User Activity Patterns
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="userActivityChart" height="300"></canvas>
                    
                    <!-- User Activity Stats -->
                    <div class="mt-4">
                        <div class="row text-center">
                            <?php $__currentLoopData = $userActivityStats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $activity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="col-6 col-md-3 mb-3">
                                <div class="card bg-light">
                                    <div class="card-body p-2">
                                        <h6 class="card-title small mb-1"><?php echo e($activity->activity_level); ?></h6>
                                        <p class="card-text h5 mb-0 text-primary"><?php echo e($activity->user_count); ?></p>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Monthly Statistics -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-dark text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-table"></i> Monthly Statistics Breakdown
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Month</th>
                                    <th class="text-end">Books Borrowed</th>
                                    <th class="text-end">Books Returned</th>
                                    <th class="text-end">Return Rate</th>
                                    <th class="text-end">Outstanding</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $monthlyStats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $month): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><?php echo e($month['month_name']); ?></td>
                                    <td class="text-end">
                                        <span class="badge bg-primary"><?php echo e($month['borrows']); ?></span>
                                    </td>
                                    <td class="text-end">
                                        <span class="badge bg-success"><?php echo e($month['returns']); ?></span>
                                    </td>
                                    <td class="text-end">
                                        <?php if($month['borrows'] > 0): ?>
                                            <?php
                                                $returnRate = round(($month['returns'] / $month['borrows']) * 100, 1);
                                            ?>
                                            <span class="badge bg-<?php echo e($returnRate >= 80 ? 'success' : ($returnRate >= 60 ? 'warning' : 'danger')); ?>">
                                                <?php echo e($returnRate); ?>%
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted">N/A</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end">
                                        <?php
                                            $outstanding = $month['borrows'] - $month['returns'];
                                        ?>
                                        <span class="badge bg-<?php echo e($outstanding > 0 ? 'warning' : 'secondary'); ?>">
                                            <?php echo e($outstanding); ?>

                                        </span>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Export Options -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-download"></i> Export Analytics
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <button class="btn btn-outline-primary me-2" onclick="exportChart('monthlyTrendsChart', 'monthly-trends')">
                                <i class="fas fa-chart-line"></i> Export Monthly Trends
                            </button>
                            <button class="btn btn-outline-info me-2" onclick="exportChart('categoryChart', 'category-distribution')">
                                <i class="fas fa-chart-pie"></i> Export Categories
                            </button>
                        </div>
                        <div class="col-md-6">
                            <button class="btn btn-outline-success me-2" onclick="exportChart('userActivityChart', 'user-activity')">
                                <i class="fas fa-users"></i> Export User Activity
                            </button>
                            <button class="btn btn-outline-dark" onclick="exportTableData()">
                                <i class="fas fa-table"></i> Export Table Data
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Monthly Trends Chart
    const monthlyTrendsCtx = document.getElementById('monthlyTrendsChart').getContext('2d');
    const monthlyTrendsChart = new Chart(monthlyTrendsCtx, {
        type: 'line',
        data: {
            labels: <?php echo json_encode(array_column($monthlyStats, 'month_name')); ?>,
            datasets: [{
                label: 'Books Borrowed',
                data: <?php echo json_encode(array_column($monthlyStats, 'borrows')); ?>,
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                tension: 0.1
            }, {
                label: 'Books Returned',
                data: <?php echo json_encode(array_column($monthlyStats, 'returns')); ?>,
                borderColor: 'rgb(255, 99, 132)',
                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                title: {
                    display: true,
                    text: 'Monthly Library Activity'
                },
                legend: {
                    position: 'top',
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Category Distribution Chart
    const categoryCtx = document.getElementById('categoryChart').getContext('2d');
    const categoryChart = new Chart(categoryCtx, {
        type: 'doughnut',
        data: {
            labels: <?php echo json_encode($categoryStats->pluck('name')); ?>,
            datasets: [{
                data: <?php echo json_encode($categoryStats->pluck('books_count')); ?>,
                backgroundColor: [
                    'rgba(255, 99, 132, 0.8)',
                    'rgba(54, 162, 235, 0.8)',
                    'rgba(255, 205, 86, 0.8)',
                    'rgba(75, 192, 192, 0.8)',
                    'rgba(153, 102, 255, 0.8)',
                    'rgba(255, 159, 64, 0.8)',
                    'rgba(199, 199, 199, 0.8)',
                    'rgba(83, 102, 255, 0.8)'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                }
            }
        }
    });

    // User Activity Chart
    const userActivityCtx = document.getElementById('userActivityChart').getContext('2d');
    const userActivityChart = new Chart(userActivityCtx, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($userActivityStats->pluck('activity_level')); ?>,
            datasets: [{
                label: 'Number of Users',
                data: <?php echo json_encode($userActivityStats->pluck('user_count')); ?>,
                backgroundColor: [
                    'rgba(255, 99, 132, 0.8)',
                    'rgba(54, 162, 235, 0.8)',
                    'rgba(255, 205, 86, 0.8)',
                    'rgba(75, 192, 192, 0.8)'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });

    // Export functions
    window.exportChart = function(chartId, filename) {
        const chart = Chart.getChart(chartId);
        const url = chart.toBase64Image();
        const link = document.createElement('a');
        link.download = filename + '.png';
        link.href = url;
        link.click();
    };

    window.exportTableData = function() {
        const table = document.querySelector('.table-striped');
        let csv = '';
        
        // Headers
        const headers = Array.from(table.querySelectorAll('thead th')).map(th => th.textContent.trim());
        csv += headers.join(',') + '\n';
        
        // Rows
        const rows = table.querySelectorAll('tbody tr');
        rows.forEach(row => {
            const cells = Array.from(row.querySelectorAll('td')).map(td => {
                // Clean up badge content
                const text = td.textContent.trim();
                return text.replace(/[,\n\r]/g, ' ');
            });
            csv += cells.join(',') + '\n';
        });
        
        // Download
        const blob = new Blob([csv], { type: 'text/csv' });
        const url = URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.download = 'monthly-statistics.csv';
        link.href = url;
        link.click();
        URL.revokeObjectURL(url);
    };
});
</script>

<style>
.card {
    border: none;
    transition: transform 0.2s;
}

.card:hover {
    transform: translateY(-2px);
}

.card-header {
    border-bottom: none;
    border-radius: 0.5rem 0.5rem 0 0 !important;
}

.badge {
    font-size: 0.8rem;
}

.table th {
    border-top: none;
    font-weight: 600;
    color: #495057;
}

.btn {
    border-radius: 0.5rem;
}

.btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

canvas {
    max-height: 400px;
}
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/resources/views/admin/analytics.blade.php ENDPATH**/ ?>