<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">Users Report</h1>
                <a href="<?php echo e(route('reports.index')); ?>" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Reports
                </a>
            </div>

            <!-- Summary Statistics -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <h5 class="card-title">Total Users</h5>
                            <h2 class="card-text"><?php echo e($users->count()); ?></h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <h5 class="card-title">Active Users</h5>
                            <h2 class="card-text"><?php echo e($activeUsers->count()); ?></h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <h5 class="card-title">Inactive Users</h5>
                            <h2 class="card-text"><?php echo e($inactiveUsers->count()); ?></h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <h5 class="card-title">Total Borrows</h5>
                            <h2 class="card-text"><?php echo e($users->sum('borrowed_books_count')); ?></h2>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Top Borrowers -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Top Borrowers</h5>
                </div>
                <div class="card-body">
                    <?php if($users->where('borrowed_books_count', '>', 0)->count() > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Rank</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Membership Type</th>
                                        <th>Total Borrows</th>
                                        <th>Current Borrows</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $users->where('borrowed_books_count', '>', 0)->take(20); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td><?php echo e($index + 1); ?></td>
                                            <td>
                                                <a href="<?php echo e(route('users.show', $user)); ?>" class="text-decoration-none">
                                                    <?php echo e($user->name); ?>

                                                </a>
                                            </td>
                                            <td><?php echo e($user->email); ?></td>
                                            <td>
                                                <span class="badge bg-<?php echo e($user->membership_type === 'premium' ? 'warning' : 'secondary'); ?>">
                                                    <?php echo e(ucfirst($user->membership_type)); ?>

                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-primary"><?php echo e($user->borrowed_books_count); ?></span>
                                            </td>
                                            <td>
                                                <span class="badge bg-info"><?php echo e($user->currently_borrowed_books_count); ?></span>
                                            </td>
                                            <td>
                                                <?php if($user->is_active): ?>
                                                    <span class="badge bg-success">Active</span>
                                                <?php else: ?>
                                                    <span class="badge bg-danger">Inactive</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p class="text-muted">No users have borrowed books yet.</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- User Activity Statistics -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Users by Membership Type</h5>
                        </div>
                        <div class="card-body">
                            <?php
                                $membershipStats = $users->groupBy('membership_type')->map(function($group) {
                                    return $group->count();
                                });
                            ?>
                            <?php $__currentLoopData = $membershipStats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type => $count): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="badge bg-<?php echo e($type === 'premium' ? 'warning' : 'secondary'); ?>">
                                        <?php echo e(ucfirst($type)); ?>

                                    </span>
                                    <span class="fw-bold"><?php echo e($count); ?> users</span>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Borrowing Activity</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span>Never Borrowed:</span>
                                <span class="fw-bold text-muted"><?php echo e($users->where('borrowed_books_count', 0)->count()); ?> users</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span>1-5 Books:</span>
                                <span class="fw-bold text-info"><?php echo e($users->whereBetween('borrowed_books_count', [1, 5])->count()); ?> users</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span>6-15 Books:</span>
                                <span class="fw-bold text-primary"><?php echo e($users->whereBetween('borrowed_books_count', [6, 15])->count()); ?> users</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span>16+ Books:</span>
                                <span class="fw-bold text-success"><?php echo e($users->where('borrowed_books_count', '>', 15)->count()); ?> users</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- All Users Table -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">All Users</h5>
                </div>
                <div class="card-body">
                    <?php if($users->count() > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-sm">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Membership</th>
                                        <th>Joined</th>
                                        <th>Total Borrows</th>
                                        <th>Current Borrows</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td>
                                                <a href="<?php echo e(route('users.show', $user)); ?>" class="text-decoration-none">
                                                    <?php echo e($user->name); ?>

                                                </a>
                                            </td>
                                            <td><?php echo e($user->email); ?></td>
                                            <td>
                                                <span class="badge bg-<?php echo e($user->membership_type === 'premium' ? 'warning' : 'secondary'); ?>">
                                                    <?php echo e(ucfirst($user->membership_type)); ?>

                                                </span>
                                            </td>
                                            <td><?php echo e($user->created_at->format('M d, Y')); ?></td>
                                            <td>
                                                <span class="badge bg-primary"><?php echo e($user->borrowed_books_count); ?></span>
                                            </td>
                                            <td>
                                                <span class="badge bg-info"><?php echo e($user->currently_borrowed_books_count); ?></span>
                                            </td>
                                            <td>
                                                <?php if($user->is_active): ?>
                                                    <span class="badge bg-success">Active</span>
                                                <?php else: ?>
                                                    <span class="badge bg-danger">Inactive</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('update', $user)): ?>
                                                    <a href="<?php echo e(route('users.edit', $user)); ?>" class="btn btn-sm btn-outline-primary">
                                                        Edit
                                                    </a>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p class="text-muted">No users found.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/resources/views/reports/users.blade.php ENDPATH**/ ?>