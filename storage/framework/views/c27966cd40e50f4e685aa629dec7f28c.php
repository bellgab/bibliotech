<?php $__env->startSection('title', 'Kölcsönzés részletei'); ?>

<?php $__env->startSection('content'); ?>
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Kölcsönzés részletei</h4>
                <div>
                    <?php if(!$borrow->returned_at): ?>
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('update', $borrow)): ?>
                            <form method="POST" action="<?php echo e(route('borrows.return', $borrow)); ?>" 
                                  class="d-inline" onsubmit="return confirm('Biztosan visszahozza a könyvet?')">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="btn btn-sm btn-success">
                                    <i class="bi bi-check"></i> Visszahozás
                                </button>
                            </form>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Könyv adatai</h6>
                        <table class="table table-borderless">
                            <tr>
                                <th width="30%">Cím:</th>
                                <td>
                                    <a href="<?php echo e(route('books.show', $borrow->book)); ?>">
                                        <?php echo e($borrow->book->title); ?>

                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <th>Szerző:</th>
                                <td><?php echo e($borrow->book->author->name); ?></td>
                            </tr>
                            <tr>
                                <th>ISBN:</th>
                                <td><?php echo e($borrow->book->isbn); ?></td>
                            </tr>
                            <tr>
                                <th>Kategória:</th>
                                <td><?php echo e($borrow->book->category->name); ?></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6>Kölcsönző adatai</h6>
                        <table class="table table-borderless">
                            <tr>
                                <th width="30%">Név:</th>
                                <td>
                                    <a href="<?php echo e(route('users.show', $borrow->user)); ?>">
                                        <?php echo e($borrow->user->name); ?>

                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <th>E-mail:</th>
                                <td><?php echo e($borrow->user->email); ?></td>
                            </tr>
                            <tr>
                                <th>Telefonszám:</th>
                                <td><?php echo e($borrow->user->phone ?? 'Nincs megadva'); ?></td>
                            </tr>
                            <tr>
                                <th>Tagsági szám:</th>
                                <td><?php echo e($borrow->user->membership_number); ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
                
                <hr>
                
                <div class="row">
                    <div class="col-md-12">
                        <h6>Kölcsönzés adatai</h6>
                        <table class="table table-borderless">
                            <tr>
                                <th width="20%">Kölcsönzés dátuma:</th>
                                <td><?php echo e($borrow->borrowed_at->format('Y. m. d. H:i')); ?></td>
                            </tr>
                            <tr>
                                <th>Lejárat:</th>
                                <td>
                                    <?php echo e($borrow->due_date->format('Y. m. d.')); ?>

                                    <?php if($borrow->is_overdue && !$borrow->returned_at): ?>
                                        <span class="badge bg-danger ms-2">
                                            <?php echo e($borrow->days_overdue); ?> nappal lejárt
                                        </span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <th>Visszahozás:</th>
                                <td>
                                    <?php if($borrow->returned_at): ?>
                                        <?php echo e($borrow->returned_at->format('Y. m. d. H:i')); ?>

                                        <span class="badge bg-success ms-2">Visszahozva</span>
                                    <?php else: ?>
                                        <em class="text-muted">Még nem hozták vissza</em>
                                        <?php if($borrow->is_overdue): ?>
                                            <span class="badge bg-danger ms-2">Lejárt</span>
                                        <?php else: ?>
                                            <span class="badge bg-warning ms-2">Kölcsönzött</span>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php if($borrow->notes): ?>
                            <tr>
                                <th>Megjegyzések:</th>
                                <td><?php echo e($borrow->notes); ?></td>
                            </tr>
                            <?php endif; ?>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <!-- Status card -->
        <div class="card mb-3">
            <div class="card-header">
                <h6 class="mb-0">Állapot</h6>
            </div>
            <div class="card-body text-center">
                <?php if($borrow->returned_at): ?>
                    <i class="bi bi-check-circle-fill text-success display-4"></i>
                    <h5 class="text-success mt-2">Visszahozva</h5>
                    <p class="text-muted"><?php echo e($borrow->returned_at->format('Y. m. d.')); ?></p>
                <?php elseif($borrow->is_overdue): ?>
                    <i class="bi bi-exclamation-triangle-fill text-danger display-4"></i>
                    <h5 class="text-danger mt-2">Lejárt</h5>
                    <p class="text-muted"><?php echo e($borrow->days_overdue); ?> napja</p>
                <?php else: ?>
                    <i class="bi bi-clock-fill text-warning display-4"></i>
                    <h5 class="text-warning mt-2">Kölcsönzött</h5>
                    <p class="text-muted">
                        Lejárat: <?php echo e($borrow->due_date->diffForHumans()); ?>

                    </p>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Action buttons -->
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Műveletek</h6>
            </div>
            <div class="card-body">
                <a href="<?php echo e(route('borrows.index')); ?>" class="btn btn-outline-secondary w-100 mb-2">
                    <i class="bi bi-arrow-left"></i> Vissza a listához
                </a>
                
                <a href="<?php echo e(route('books.show', $borrow->book)); ?>" class="btn btn-outline-primary w-100 mb-2">
                    <i class="bi bi-book"></i> Könyv megtekintése
                </a>
                
                <a href="<?php echo e(route('users.show', $borrow->user)); ?>" class="btn btn-outline-info w-100">
                    <i class="bi bi-person"></i> Felhasználó megtekintése
                </a>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/resources/views/borrows/show.blade.php ENDPATH**/ ?>