<?php $__env->startSection('title', 'Felhasználók'); ?>

<?php $__env->startSection('content'); ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Felhasználók</h1>
    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create', App\Models\User::class)): ?>
        <a href="<?php echo e(route('users.create')); ?>" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Új felhasználó
        </a>
    <?php endif; ?>
</div>

<?php if($users->count() > 0): ?>
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Név</th>
                            <th>E-mail</th>
                            <th>Tagsági szám</th>
                            <th>Szerepkör</th>
                            <th>Telefonszám</th>
                            <th>Aktív kölcsönzések</th>
                            <th>Műveletek</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td>
                                    <strong><?php echo e($user->name); ?></strong>
                                    <?php if($user->email_verified_at): ?>
                                        <i class="bi bi-check-circle-fill text-success" 
                                           title="Ellenőrzött e-mail"></i>
                                    <?php else: ?>
                                        <i class="bi bi-exclamation-circle-fill text-warning" 
                                           title="Nem ellenőrzött e-mail"></i>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo e($user->email); ?></td>
                                <td>
                                    <code><?php echo e($user->membership_number); ?></code>
                                </td>
                                <td>
                                    <?php switch($user->role):
                                        case ('admin'): ?>
                                            <span class="badge bg-danger">Admin</span>
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
                                <td><?php echo e($user->phone ?? '-'); ?></td>
                                <td>
                                    <?php
                                        $activeBorrows = $user->borrows()->whereNull('returned_at')->count();
                                    ?>
                                    <?php if($activeBorrows > 0): ?>
                                        <span class="badge bg-info"><?php echo e($activeBorrows); ?> db</span>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="<?php echo e(route('users.show', $user)); ?>" 
                                           class="btn btn-outline-primary">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('update', $user)): ?>
                                            <a href="<?php echo e(route('users.edit', $user)); ?>" 
                                               class="btn btn-outline-secondary">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                        <?php endif; ?>
                                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('delete', $user)): ?>
                                            <?php if($user->id !== auth()->id()): ?>
                                                <form method="POST" action="<?php echo e(route('users.destroy', $user)); ?>" 
                                                      class="d-inline" onsubmit="return confirm('Biztosan törli ezt a felhasználót?')">
                                                    <?php echo csrf_field(); ?>
                                                    <?php echo method_field('DELETE'); ?>
                                                    <button type="submit" class="btn btn-outline-danger">
                                                        <i class="bi bi-trash"></i>
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
        <?php echo e($users->links()); ?>

    </div>
<?php else: ?>
    <div class="text-center py-5">
        <i class="bi bi-people display-1 text-muted"></i>
        <h3 class="text-muted mt-3">Nincs felhasználó</h3>
        <p class="text-muted">Még nem adtak hozzá felhasználót a rendszerhez.</p>
        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create', App\Models\User::class)): ?>
            <a href="<?php echo e(route('users.create')); ?>" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Első felhasználó hozzáadása
            </a>
        <?php endif; ?>
    </div>
<?php endif; ?>

<!-- Statistics Cards -->
<div class="row mt-4">
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="card-title"><?php echo e($users->where('role', 'admin')->count()); ?></h5>
                <p class="card-text text-muted">Adminisztrátor</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="card-title"><?php echo e($users->where('role', 'librarian')->count()); ?></h5>
                <p class="card-text text-muted">Könyvtáros</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="card-title"><?php echo e($users->where('role', 'member')->count()); ?></h5>
                <p class="card-text text-muted">Tag</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="card-title"><?php echo e($users->whereNotNull('email_verified_at')->count()); ?></h5>
                <p class="card-text text-muted">Ellenőrzött e-mail</p>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/resources/views/users/index.blade.php ENDPATH**/ ?>