<?php $__env->startSection('title', $user->name); ?>

<?php $__env->startSection('content'); ?>
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0"><?php echo e($user->name); ?></h4>
                <div>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('update', $user)): ?>
                        <a href="<?php echo e(route('users.edit', $user)); ?>" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-pencil"></i> Szerkesztés
                        </a>
                    <?php endif; ?>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('delete', $user)): ?>
                        <?php if($user->id !== auth()->id()): ?>
                            <form method="POST" action="<?php echo e(route('users.destroy', $user)); ?>" 
                                  class="d-inline" onsubmit="return confirm('Biztosan törli ezt a felhasználót?')">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('DELETE'); ?>
                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                    <i class="bi bi-trash"></i> Törlés
                                </button>
                            </form>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th width="30%">E-mail:</th>
                                <td>
                                    <?php echo e($user->email); ?>

                                    <?php if($user->email_verified_at): ?>
                                        <i class="bi bi-check-circle-fill text-success ms-1" 
                                           title="Ellenőrzött e-mail"></i>
                                    <?php else: ?>
                                        <i class="bi bi-exclamation-circle-fill text-warning ms-1" 
                                           title="Nem ellenőrzött e-mail"></i>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <th>Tagsági szám:</th>
                                <td><code><?php echo e($user->membership_number); ?></code></td>
                            </tr>
                            <tr>
                                <th>Szerepkör:</th>
                                <td>
                                    <?php switch($user->role):
                                        case ('admin'): ?>
                                            <span class="badge bg-danger">Adminisztrátor</span>
                                            <?php break; ?>
                                        <?php case ('librarian'): ?>
                                            <span class="badge bg-warning">Könyvtáros</span>
                                            <?php break; ?>
                                        <?php case ('member'): ?>
                                            <span class="badge bg-primary">Tag</span>
                                            <?php break; ?>
                                        <?php default: ?>
                                            <span class="badge bg-secondary"><?php echo e(ucfirst($user->role)); ?></span>
                                    <?php endswitch; ?>
                                </td>
                            </tr>
                            <tr>
                                <th>Telefonszám:</th>
                                <td><?php echo e($user->phone ?? 'Nincs megadva'); ?></td>
                            </tr>
                            <tr>
                                <th>Regisztráció:</th>
                                <td><?php echo e($user->created_at->format('Y. m. d.')); ?></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <?php if($user->address): ?>
                            <h6>Cím:</h6>
                            <p><?php echo e($user->address); ?></p>
                        <?php endif; ?>
                        
                        <?php
                            $activeBorrows = $user->borrows()->whereNull('returned_at')->count();
                            $totalBorrows = $user->borrows()->count();
                            $overdueBorrows = $user->borrows()->whereNull('returned_at')
                                ->where('due_date', '<', now())->count();
                        ?>
                        
                        <h6>Kölcsönzési statisztikák:</h6>
                        <ul class="list-unstyled">
                            <li><strong>Aktív kölcsönzések:</strong> <?php echo e($activeBorrows); ?> db</li>
                            <li><strong>Összes kölcsönzés:</strong> <?php echo e($totalBorrows); ?> db</li>
                            <?php if($overdueBorrows > 0): ?>
                                <li><strong>Lejárt kölcsönzések:</strong> 
                                    <span class="text-danger"><?php echo e($overdueBorrows); ?> db</span>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <!-- Quick actions -->
        <div class="card mb-3">
            <div class="card-header">
                <h6 class="mb-0">Műveletek</h6>
            </div>
            <div class="card-body">
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create', App\Models\BookBorrowing::class)): ?>
                    <a href="<?php echo e(route('borrows.create', ['user_id' => $user->id])); ?>" 
                       class="btn btn-success w-100 mb-2">
                        <i class="bi bi-book"></i> Új kölcsönzés
                    </a>
                <?php endif; ?>
                
                <a href="<?php echo e(route('users.index')); ?>" class="btn btn-outline-secondary w-100">
                    <i class="bi bi-arrow-left"></i> Vissza a listához
                </a>
            </div>
        </div>
        
        <!-- Status card -->
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Állapot</h6>
            </div>
            <div class="card-body text-center">
                <?php if($overdueBorrows > 0): ?>
                    <i class="bi bi-exclamation-triangle-fill text-danger display-4"></i>
                    <h6 class="text-danger mt-2">Lejárt kölcsönzés</h6>
                <?php elseif($activeBorrows > 0): ?>
                    <i class="bi bi-clock-fill text-warning display-4"></i>
                    <h6 class="text-warning mt-2">Aktív kölcsönzés</h6>
                <?php else: ?>
                    <i class="bi bi-check-circle-fill text-success display-4"></i>
                    <h6 class="text-success mt-2">Rendben</h6>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Borrowing history -->
<?php if($user->borrows()->count() > 0): ?>
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Kölcsönzési előzmények</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Könyv</th>
                                    <th>Kölcsönzés</th>
                                    <th>Lejárat</th>
                                    <th>Visszahozás</th>
                                    <th>Állapot</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $user->borrows()->with('book')->latest()->limit(10)->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $borrow): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr class="<?php echo e($borrow->is_overdue && !$borrow->returned_at ? 'table-warning' : ''); ?>">
                                        <td>
                                            <a href="<?php echo e(route('books.show', $borrow->book)); ?>">
                                                <?php echo e($borrow->book->title); ?>

                                            </a>
                                        </td>
                                        <td><?php echo e($borrow->borrowed_at->format('Y.m.d')); ?></td>
                                        <td><?php echo e($borrow->due_date->format('Y.m.d')); ?></td>
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
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <?php if($user->borrows()->count() > 10): ?>
                        <div class="text-center mt-2">
                            <a href="<?php echo e(route('borrows.index', ['user_id' => $user->id])); ?>" 
                               class="btn btn-sm btn-outline-primary">
                                Összes kölcsönzés megtekintése
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/resources/views/users/show.blade.php ENDPATH**/ ?>