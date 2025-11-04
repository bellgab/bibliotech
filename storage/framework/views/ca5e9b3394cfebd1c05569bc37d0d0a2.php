<?php $__env->startSection('title', 'Kölcsönzések'); ?>

<?php $__env->startSection('content'); ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Kölcsönzések</h1>
    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create', App\Models\BookBorrowing::class)): ?>
        <a href="<?php echo e(route('borrows.create')); ?>" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Új kölcsönzés
        </a>
    <?php endif; ?>
</div>

<?php if($borrows->count() > 0): ?>
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Könyv</th>
                            <th>Kölcsönző</th>
                            <th>Kölcsönzés dátuma</th>
                            <th>Lejárat</th>
                            <th>Visszahozás</th>
                            <th>Állapot</th>
                            <th>Műveletek</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $borrows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $borrow): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr class="<?php echo e($borrow->is_overdue ? 'table-danger' : ''); ?>">
                                <td>
                                    <strong><?php echo e($borrow->book->title); ?></strong><br>
                                    <small class="text-muted"><?php echo e($borrow->book->author->name); ?></small>
                                </td>
                                <td>
                                    <?php echo e($borrow->user->name); ?><br>
                                    <small class="text-muted"><?php echo e($borrow->user->email); ?></small>
                                </td>
                                <td><?php echo e($borrow->borrowed_at->format('Y.m.d')); ?></td>
                                <td>
                                    <?php echo e($borrow->due_date->format('Y.m.d')); ?>

                                    <?php if($borrow->is_overdue && !$borrow->returned_at): ?>
                                        <span class="badge bg-danger">Lejárt</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if($borrow->returned_at): ?>
                                        <?php echo e($borrow->returned_at->format('Y.m.d')); ?>

                                    <?php else: ?>
                                        <em class="text-muted">-</em>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if($borrow->returned_at): ?>
                                        <span class="badge bg-success">Visszahozva</span>
                                    <?php elseif($borrow->is_overdue): ?>
                                        <span class="badge bg-danger">Lejárt</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning">Kölcsönzött</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="<?php echo e(route('borrows.show', $borrow)); ?>" 
                                           class="btn btn-outline-primary">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <?php if(!$borrow->returned_at): ?>
                                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('update', $borrow)): ?>
                                                <form method="POST" action="<?php echo e(route('borrows.return', $borrow)); ?>" 
                                                      class="d-inline">
                                                    <?php echo csrf_field(); ?>
                                                    <button type="submit" class="btn btn-outline-success"
                                                            onclick="return confirm('Biztosan visszahozza a könyvet?')">
                                                        <i class="bi bi-check"></i>
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Pagination -->
    <div class="d-flex justify-content-center mt-3">
        <?php echo e($borrows->links()); ?>

    </div>
<?php else: ?>
    <div class="text-center py-5">
        <i class="bi bi-bookmark display-1 text-muted"></i>
        <h3 class="text-muted mt-3">Nincs kölcsönzés</h3>
        <p class="text-muted">Még nem történt kölcsönzés a rendszerben.</p>
        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create', App\Models\BookBorrowing::class)): ?>
            <a href="<?php echo e(route('borrows.create')); ?>" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Első kölcsönzés
            </a>
        <?php endif; ?>
    </div>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/resources/views/borrows/index.blade.php ENDPATH**/ ?>