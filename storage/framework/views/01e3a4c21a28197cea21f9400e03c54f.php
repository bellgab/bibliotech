<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">Books Report</h1>
                <a href="<?php echo e(route('reports.index')); ?>" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Reports
                </a>
            </div>

            <!-- Summary Statistics -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <h5 class="card-title">Total Books</h5>
                            <h2 class="card-text"><?php echo e($books->count()); ?></h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <h5 class="card-title">Total Borrows</h5>
                            <h2 class="card-text"><?php echo e($books->sum('borrows_count')); ?></h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <h5 class="card-title">Average Borrows per Book</h5>
                            <h2 class="card-text"><?php echo e($books->count() > 0 ? round($books->sum('borrows_count') / $books->count(), 1) : 0); ?></h2>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Most Popular Books -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Most Popular Books (Top 10)</h5>
                </div>
                <div class="card-body">
                    <?php if($mostPopular->count() > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Rank</th>
                                        <th>Title</th>
                                        <th>Author</th>
                                        <th>Category</th>
                                        <th>Times Borrowed</th>
                                        <th>Available Copies</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $mostPopular; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $book): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td><?php echo e($index + 1); ?></td>
                                            <td>
                                                <a href="<?php echo e(route('books.show', $book)); ?>" class="text-decoration-none">
                                                    <?php echo e($book->title); ?>

                                                </a>
                                            </td>
                                            <td><?php echo e($book->author->name ?? 'Unknown'); ?></td>
                                            <td><?php echo e($book->category->name ?? 'Uncategorized'); ?></td>
                                            <td>
                                                <span class="badge bg-primary"><?php echo e($book->borrows_count); ?></span>
                                            </td>
                                            <td><?php echo e($book->available_copies); ?></td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p class="text-muted">No books found.</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Least Popular Books -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Least Popular Books (Bottom 10)</h5>
                </div>
                <div class="card-body">
                    <?php if($leastPopular->count() > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>Author</th>
                                        <th>Category</th>
                                        <th>Times Borrowed</th>
                                        <th>Available Copies</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $leastPopular; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $book): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td>
                                                <a href="<?php echo e(route('books.show', $book)); ?>" class="text-decoration-none">
                                                    <?php echo e($book->title); ?>

                                                </a>
                                            </td>
                                            <td><?php echo e($book->author->name ?? 'Unknown'); ?></td>
                                            <td><?php echo e($book->category->name ?? 'Uncategorized'); ?></td>
                                            <td>
                                                <span class="badge bg-<?php echo e($book->borrows_count == 0 ? 'danger' : 'warning'); ?>">
                                                    <?php echo e($book->borrows_count); ?>

                                                </span>
                                            </td>
                                            <td><?php echo e($book->available_copies); ?></td>
                                            <td>
                                                <?php if($book->borrows_count == 0): ?>
                                                    <span class="badge bg-danger">Never Borrowed</span>
                                                <?php else: ?>
                                                    <span class="badge bg-warning">Low Interest</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p class="text-muted">No books found.</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- All Books Table -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">All Books</h5>
                </div>
                <div class="card-body">
                    <?php if($books->count() > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-sm">
                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>Author</th>
                                        <th>Category</th>
                                        <th>ISBN</th>
                                        <th>Total Copies</th>
                                        <th>Available</th>
                                        <th>Times Borrowed</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $books; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $book): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td>
                                                <a href="<?php echo e(route('books.show', $book)); ?>" class="text-decoration-none">
                                                    <?php echo e($book->title); ?>

                                                </a>
                                            </td>
                                            <td><?php echo e($book->author->name ?? 'Unknown'); ?></td>
                                            <td><?php echo e($book->category->name ?? 'Uncategorized'); ?></td>
                                            <td><?php echo e($book->isbn ?? 'N/A'); ?></td>
                                            <td><?php echo e($book->total_copies); ?></td>
                                            <td><?php echo e($book->available_copies); ?></td>
                                            <td>
                                                <span class="badge bg-info"><?php echo e($book->borrows_count); ?></span>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p class="text-muted">No books found.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/resources/views/reports/books.blade.php ENDPATH**/ ?>