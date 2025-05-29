<?php $__env->startSection('title', 'Jelentések'); ?>

<?php $__env->startSection('content'); ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Jelentések és Statisztikák</h1>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <i class="bi bi-book display-4 text-primary"></i>
                <h3 class="mt-2"><?php echo e($stats['total_books'] ?? 0); ?></h3>
                <p class="text-muted">Összes könyv</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <i class="bi bi-people display-4 text-info"></i>
                <h3 class="mt-2"><?php echo e($stats['total_users'] ?? 0); ?></h3>
                <p class="text-muted">Regisztrált felhasználó</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <i class="bi bi-bookmark display-4 text-warning"></i>
                <h3 class="mt-2"><?php echo e($stats['active_borrows'] ?? 0); ?></h3>
                <p class="text-muted">Aktív kölcsönzés</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <i class="bi bi-exclamation-triangle display-4 text-danger"></i>
                <h3 class="mt-2"><?php echo e($stats['overdue_borrows'] ?? 0); ?></h3>
                <p class="text-muted">Lejárt kölcsönzés</p>
            </div>
        </div>
    </div>
</div>

<!-- Report Links -->
<div class="row">
    <div class="col-md-6">
        <div class="list-group">
            <div class="list-group-item list-group-item-action d-flex justify-content-between align-items-center bg-light">
                <strong>Könyv jelentések</strong>
            </div>
            <a href="<?php echo e(route('reports.books')); ?>" class="list-group-item list-group-item-action">
                <div class="d-flex w-100 justify-content-between">
                    <h6 class="mb-1">Könyv statisztikák</h6>
                    <small><i class="bi bi-arrow-right"></i></small>
                </div>
                <p class="mb-1">Legnépszerűbb és legkevésbé kölcsönzött könyvek</p>
            </a>
            <a href="<?php echo e(route('reports.borrows')); ?>" class="list-group-item list-group-item-action">
                <div class="d-flex w-100 justify-content-between">
                    <h6 class="mb-1">Kölcsönzési jelentés</h6>
                    <small><i class="bi bi-arrow-right"></i></small>
                </div>
                <p class="mb-1">Kölcsönzési statisztikák és trendek</p>
            </a>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="list-group">
            <div class="list-group-item list-group-item-action d-flex justify-content-between align-items-center bg-light">
                <strong>Felhasználói jelentések</strong>
            </div>
            <a href="<?php echo e(route('reports.users')); ?>" class="list-group-item list-group-item-action">
                <div class="d-flex w-100 justify-content-between">
                    <h6 class="mb-1">Felhasználói statisztikák</h6>
                    <small><i class="bi bi-arrow-right"></i></small>
                </div>
                <p class="mb-1">Aktív és inaktív felhasználók</p>
            </a>
            <a href="<?php echo e(route('reports.fines')); ?>" class="list-group-item list-group-item-action">
                <div class="d-flex w-100 justify-content-between">
                    <h6 class="mb-1">Késedelmi díjak</h6>
                    <small><i class="bi bi-arrow-right"></i></small>
                </div>
                <p class="mb-1">Lejárt kölcsönzések és díjak</p>
            </a>
        </div>
    </div>
</div>

<!-- Quick Stats -->
<?php if(isset($stats)): ?>
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Gyors statisztikák</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <h6>Könyvek</h6>
                        <ul class="list-unstyled">
                            <li>Összes: <?php echo e($stats['total_books'] ?? 0); ?> db</li>
                            <li>Elérhető: <?php echo e($stats['available_books'] ?? 0); ?> db</li>
                            <li>Kölcsönzött: <?php echo e($stats['borrowed_books'] ?? 0); ?> db</li>
                        </ul>
                    </div>
                    <div class="col-md-4">
                        <h6>Felhasználók</h6>
                        <ul class="list-unstyled">
                            <li>Összes: <?php echo e($stats['total_users'] ?? 0); ?> fő</li>
                            <li>Aktív tagok: <?php echo e($stats['active_members'] ?? 0); ?> fő</li>
                            <li>Könyvtárosok: <?php echo e($stats['librarians'] ?? 0); ?> fő</li>
                        </ul>
                    </div>
                    <div class="col-md-4">
                        <h6>Kölcsönzések</h6>
                        <ul class="list-unstyled">
                            <li>Mai nap: <?php echo e($stats['today_borrows'] ?? 0); ?> db</li>
                            <li>Ez a hét: <?php echo e($stats['week_borrows'] ?? 0); ?> db</li>
                            <li>Ez a hónap: <?php echo e($stats['month_borrows'] ?? 0); ?> db</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/resources/views/reports/index.blade.php ENDPATH**/ ?>