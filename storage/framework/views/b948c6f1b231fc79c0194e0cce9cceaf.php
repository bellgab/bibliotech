<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">Fines Report</h1>
                <a href="<?php echo e(route('reports.index')); ?>" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Reports
                </a>
            </div>

            <!-- Summary Statistics -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <h5 class="card-title">Total Fines Collected</h5>
                            <h2 class="card-text">$<?php echo e(number_format($totalFines, 2)); ?></h2>
                            <small>From returned books</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <h5 class="card-title">Unpaid Fines</h5>
                            <h2 class="card-text">$<?php echo e(number_format($unpaidFines, 2)); ?></h2>
                            <small>From overdue books</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <h5 class="card-title">Total Outstanding</h5>
                            <h2 class="card-text">$<?php echo e(number_format($totalFines + $unpaidFines, 2)); ?></h2>
                            <small>All time fines</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Current Unpaid Fines (from overdue books) -->
            <?php if($unpaidFines > 0): ?>
                <div class="card mb-4">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0">
                            <i class="fas fa-exclamation-triangle"></i> Current Unpaid Fines (Overdue Books)
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-warning">
                            <strong>Note:</strong> These fines are calculated for currently overdue books and will be applied when the books are returned.
                        </div>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Book</th>
                                        <th>Borrower</th>
                                        <th>Due Date</th>
                                        <th>Days Overdue</th>
                                        <th>Fine Amount</th>
                                        <th>Contact</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        $overdueBorrows = \App\Models\BookBorrowing::whereNull('returned_at')
                                            ->where('due_date', '<', now())
                                            ->with(['book.author', 'user'])
                                            ->orderBy('due_date', 'asc')
                                            ->get();
                                    ?>
                                    <?php $__currentLoopData = $overdueBorrows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $borrow): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php
                                            $daysOverdue = now()->diffInDays($borrow->due_date);
                                            $calculatedFine = $borrow->calculateFine();
                                        ?>
                                        <tr class="table-warning">
                                            <td>
                                                <a href="<?php echo e(route('books.show', $borrow->book)); ?>" class="text-decoration-none">
                                                    <?php echo e($borrow->book->title); ?>

                                                </a>
                                                <br>
                                                <small class="text-muted">by <?php echo e($borrow->book->author->name ?? 'Unknown'); ?></small>
                                            </td>
                                            <td>
                                                <a href="<?php echo e(route('users.show', $borrow->user)); ?>" class="text-decoration-none">
                                                    <?php echo e($borrow->user->name); ?>

                                                </a>
                                                <br>
                                                <small class="text-muted"><?php echo e($borrow->user->email); ?></small>
                                            </td>
                                            <td><?php echo e($borrow->due_date->format('M d, Y')); ?></td>
                                            <td>
                                                <span class="badge bg-danger">
                                                    <?php echo e($daysOverdue); ?> <?php echo e($daysOverdue == 1 ? 'day' : 'days'); ?>

                                                </span>
                                            </td>
                                            <td>
                                                <span class="fw-bold text-danger">
                                                    $<?php echo e(number_format($calculatedFine, 2)); ?>

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

            <!-- Fines History -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Fines History (Paid)</h5>
                </div>
                <div class="card-body">
                    <?php if($returnedOverdueBooks->count() > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Book</th>
                                        <th>Borrower</th>
                                        <th>Borrowed Date</th>
                                        <th>Due Date</th>
                                        <th>Returned Date</th>
                                        <th>Days Late</th>
                                        <th>Fine Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $returnedOverdueBooks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $borrow): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php
                                            $daysLate = $borrow->returned_at && $borrow->due_date 
                                                ? $borrow->returned_at->diffInDays($borrow->due_date) 
                                                : 0;
                                            $fineAmount = $daysLate * 5.0; // $5 per day fine
                                        ?>
                                        <tr>
                                            <td>
                                                <a href="<?php echo e(route('books.show', $borrow->book)); ?>" class="text-decoration-none">
                                                    <?php echo e($borrow->book->title); ?>

                                                </a>
                                                <br>
                                                <small class="text-muted">by <?php echo e($borrow->book->author->name ?? 'Unknown'); ?></small>
                                            </td>
                                            <td>
                                                <a href="<?php echo e(route('users.show', $borrow->user)); ?>" class="text-decoration-none">
                                                    <?php echo e($borrow->user->name); ?>

                                                </a>
                                                <br>
                                                <small class="text-muted"><?php echo e($borrow->user->email); ?></small>
                                            </td>
                                            <td><?php echo e($borrow->borrowed_at->format('M d, Y')); ?></td>
                                            <td><?php echo e($borrow->due_date->format('M d, Y')); ?></td>
                                            <td>
                                                <?php echo e($borrow->returned_at ? $borrow->returned_at->format('M d, Y') : 'Not returned'); ?>

                                            </td>
                                            <td>
                                                <?php if($daysLate > 0): ?>
                                                    <span class="badge bg-warning">
                                                        <?php echo e($daysLate); ?> <?php echo e($daysLate == 1 ? 'day' : 'days'); ?>

                                                    </span>
                                                <?php else: ?>
                                                    <span class="badge bg-success">On time</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span class="fw-bold text-success">
                                                    $<?php echo e(number_format($fineAmount, 2)); ?>

                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination could go here if needed -->
                        <?php if($returnedOverdueBooks->count() >= 50): ?>
                            <div class="mt-3">
                                <p class="text-muted">Showing recent fines. Contact admin for complete history.</p>
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <i class="fas fa-dollar-sign text-muted" style="font-size: 3rem;"></i>
                            <p class="text-muted mt-3">No fines have been collected yet.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Fine Statistics -->
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Fine Summary</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span>Books with Fines:</span>
                                <span class="fw-bold"><?php echo e($returnedOverdueBooks->count()); ?></span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span>Average Fine:</span>
                                <span class="fw-bold">
                                    <?php
                                        $avgFine = $returnedOverdueBooks->count() > 0 
                                            ? $returnedOverdueBooks->map(function($borrow) {
                                                $daysLate = $borrow->returned_at && $borrow->due_date 
                                                    ? $borrow->returned_at->diffInDays($borrow->due_date) 
                                                    : 0;
                                                return $daysLate * 5.0;
                                            })->avg() 
                                            : 0;
                                    ?>
                                    $<?php echo e(number_format($avgFine, 2)); ?>

                                </span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span>Highest Fine:</span>
                                <span class="fw-bold text-danger">
                                    <?php
                                        $maxFine = $returnedOverdueBooks->count() > 0 
                                            ? $returnedOverdueBooks->map(function($borrow) {
                                                $daysLate = $borrow->returned_at && $borrow->due_date 
                                                    ? $borrow->returned_at->diffInDays($borrow->due_date) 
                                                    : 0;
                                                return $daysLate * 5.0;
                                            })->max() 
                                            : 0;
                                    ?>
                                    $<?php echo e(number_format($maxFine, 2)); ?>

                                </span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span>Total Collected:</span>
                                <span class="fw-bold text-success">
                                    $<?php echo e(number_format($totalFines, 2)); ?>

                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Outstanding Fines</h5>
                        </div>
                        <div class="card-body">
                            <?php
                                $currentOverdue = \App\Models\BookBorrowing::whereNull('returned_at')
                                    ->where('due_date', '<', now())
                                    ->count();
                            ?>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span>Overdue Books:</span>
                                <span class="fw-bold text-warning"><?php echo e($currentOverdue); ?></span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span>Estimated Fines:</span>
                                <span class="fw-bold text-warning">
                                    $<?php echo e(number_format($unpaidFines, 2)); ?>

                                </span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span>When Collected:</span>
                                <span class="fw-bold text-info">
                                    $<?php echo e(number_format($totalFines + $unpaidFines, 2)); ?>

                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/resources/views/reports/fines.blade.php ENDPATH**/ ?>