<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">Borrows Report</h1>
                <a href="<?php echo e(route('reports.index')); ?>" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Reports
                </a>
            </div>

            <!-- Summary Statistics -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <h5 class="card-title">This Month</h5>
                            <h2 class="card-text"><?php echo e($monthlyBorrows); ?></h2>
                            <small>New borrows</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <h5 class="card-title">Last Month</h5>
                            <h2 class="card-text"><?php echo e($lastMonthBorrows); ?></h2>
                            <small>
                                <?php if($lastMonthBorrows > 0): ?>
                                    <?php
                                        $change = (($monthlyBorrows - $lastMonthBorrows) / $lastMonthBorrows) * 100;
                                    ?>
                                    <?php echo e($change > 0 ? '+' : ''); ?><?php echo e(round($change, 1)); ?>% change
                                <?php else: ?>
                                    New activity
                                <?php endif; ?>
                            </small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <h5 class="card-title">Active Borrows</h5>
                            <h2 class="card-text"><?php echo e($activeBorrows->count()); ?></h2>
                            <small>Currently borrowed</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-danger text-white">
                        <div class="card-body">
                            <h5 class="card-title">Overdue</h5>
                            <h2 class="card-text"><?php echo e($overdueBorrows->count()); ?></h2>
                            <small>Need attention</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Overdue Books -->
            <?php if($overdueBorrows->count() > 0): ?>
                <div class="card mb-4">
                    <div class="card-header bg-danger text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-exclamation-triangle"></i> Overdue Books
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Book</th>
                                        <th>Author</th>
                                        <th>Borrower</th>
                                        <th>Due Date</th>
                                        <th>Days Overdue</th>
                                        <th>Contact</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $overdueBorrows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $borrow): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php
                                            $daysOverdue = now()->diffInDays($borrow->due_date);
                                        ?>
                                        <tr class="table-danger">
                                            <td>
                                                <a href="<?php echo e(route('books.show', $borrow->book)); ?>" class="text-decoration-none">
                                                    <?php echo e($borrow->book->title); ?>

                                                </a>
                                            </td>
                                            <td><?php echo e($borrow->book->author->name ?? 'Unknown'); ?></td>
                                            <td>
                                                <a href="<?php echo e(route('users.show', $borrow->user)); ?>" class="text-decoration-none">
                                                    <?php echo e($borrow->user->name); ?>

                                                </a>
                                            </td>
                                            <td><?php echo e($borrow->due_date->format('M d, Y')); ?></td>
                                            <td>
                                                <span class="badge bg-danger">
                                                    <?php echo e($daysOverdue); ?> <?php echo e($daysOverdue == 1 ? 'day' : 'days'); ?>

                                                </span>
                                            </td>
                                            <td>
                                                <a href="mailto:<?php echo e($borrow->user->email); ?>" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-envelope"></i>
                                                </a>
                                            </td>
                                            <td>
                                                <a href="<?php echo e(route('borrows.show', $borrow)); ?>" class="btn btn-sm btn-outline-secondary">
                                                    View
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Active Borrows -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Active Borrows</h5>
                </div>
                <div class="card-body">
                    <?php if($activeBorrows->count() > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Book</th>
                                        <th>Author</th>
                                        <th>Borrower</th>
                                        <th>Borrowed Date</th>
                                        <th>Due Date</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $activeBorrows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $borrow): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php
                                            $isOverdue = $borrow->due_date < now();
                                            $daysUntilDue = now()->diffInDays($borrow->due_date, false);
                                        ?>
                                        <tr class="<?php echo e($isOverdue ? 'table-danger' : ($daysUntilDue <= 3 ? 'table-warning' : '')); ?>">
                                            <td>
                                                <a href="<?php echo e(route('books.show', $borrow->book)); ?>" class="text-decoration-none">
                                                    <?php echo e($borrow->book->title); ?>

                                                </a>
                                            </td>
                                            <td><?php echo e($borrow->book->author->name ?? 'Unknown'); ?></td>
                                            <td>
                                                <a href="<?php echo e(route('users.show', $borrow->user)); ?>" class="text-decoration-none">
                                                    <?php echo e($borrow->user->name); ?>

                                                </a>
                                            </td>
                                            <td><?php echo e($borrow->borrowed_at->format('M d, Y')); ?></td>
                                            <td><?php echo e($borrow->due_date->format('M d, Y')); ?></td>
                                            <td>
                                                <?php if($isOverdue): ?>
                                                    <span class="badge bg-danger">Overdue</span>
                                                <?php elseif($daysUntilDue <= 3): ?>
                                                    <span class="badge bg-warning">Due Soon</span>
                                                <?php else: ?>
                                                    <span class="badge bg-success">Active</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="<?php echo e(route('borrows.show', $borrow)); ?>" class="btn btn-sm btn-outline-secondary">
                                                        View
                                                    </a>
                                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('update', $borrow)): ?>
                                                        <form action="<?php echo e(route('borrows.return', $borrow)); ?>" method="POST" class="d-inline">
                                                            <?php echo csrf_field(); ?>
                                                            <button type="submit" class="btn btn-sm btn-outline-success"
                                                                    onclick="return confirm('Mark this book as returned?')">
                                                                Return
                                                            </button>
                                                        </form>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <i class="fas fa-book text-muted" style="font-size: 3rem;"></i>
                            <p class="text-muted mt-3">No active borrows at the moment.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Borrowing Trends -->
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Monthly Comparison</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span>This Month:</span>
                                <span class="fw-bold text-primary"><?php echo e($monthlyBorrows); ?> borrows</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span>Last Month:</span>
                                <span class="fw-bold text-info"><?php echo e($lastMonthBorrows); ?> borrows</span>
                            </div>
                            <?php if($lastMonthBorrows > 0): ?>
                                <?php
                                    $change = (($monthlyBorrows - $lastMonthBorrows) / $lastMonthBorrows) * 100;
                                ?>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span>Change:</span>
                                    <span class="fw-bold text-<?php echo e($change >= 0 ? 'success' : 'danger'); ?>">
                                        <?php echo e($change > 0 ? '+' : ''); ?><?php echo e(round($change, 1)); ?>%
                                    </span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Status Overview</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span>On Time:</span>
                                <span class="fw-bold text-success"><?php echo e($activeBorrows->filter(function($b) { return $b->due_date >= now(); })->count()); ?></span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span>Due Soon (3 days):</span>
                                <span class="fw-bold text-warning"><?php echo e($activeBorrows->filter(function($b) { return $b->due_date >= now() && now()->diffInDays($b->due_date) <= 3; })->count()); ?></span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span>Overdue:</span>
                                <span class="fw-bold text-danger"><?php echo e($overdueBorrows->count()); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/resources/views/reports/borrows.blade.php ENDPATH**/ ?>