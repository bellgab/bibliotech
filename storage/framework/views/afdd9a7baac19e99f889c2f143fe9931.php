<?php $__env->startSection('title', 'Könyvek'); ?>

<?php $__env->startSection('content'); ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Könyvek</h1>
    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create', App\Models\Book::class)): ?>
        <a href="<?php echo e(route('books.create')); ?>" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Új könyv
        </a>
    <?php endif; ?>
</div>

<!-- Search and Filter Form -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="<?php echo e(route('books.index')); ?>" class="row g-3">
            <div class="col-md-4">
                <label for="search" class="form-label">Keresés</label>
                <input type="text" class="form-control" id="search" name="search" 
                       value="<?php echo e(request('search')); ?>" placeholder="Cím, ISBN vagy szerző...">
            </div>
            <div class="col-md-3">
                <label for="category" class="form-label">Kategória</label>
                <select class="form-select" id="category" name="category">
                    <option value="">Összes kategória</option>
                    <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($category->id); ?>" 
                                <?php echo e(request('category') == $category->id ? 'selected' : ''); ?>>
                            <?php echo e($category->name); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="col-md-3">
                <label for="available" class="form-label">Elérhetőség</label>
                <select class="form-select" id="available" name="available">
                    <option value="">Összes könyv</option>
                    <option value="1" <?php echo e(request('available') == '1' ? 'selected' : ''); ?>>
                        Csak elérhető
                    </option>
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-secondary me-2">
                    <i class="bi bi-search"></i> Keresés
                </button>
                <a href="<?php echo e(route('books.index')); ?>" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-clockwise"></i>
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Books Grid -->
<?php if($books->count() > 0): ?>
    <div class="row">
        <?php $__currentLoopData = $books; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $book): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo e($book->title); ?></h5>
                        <p class="card-text">
                            <strong>Szerző:</strong> <?php echo e($book->author->name); ?><br>
                            <strong>Kategória:</strong> <?php echo e($book->category->name); ?><br>
                            <strong>ISBN:</strong> <?php echo e($book->isbn); ?><br>
                            <strong>Elérhető:</strong> 
                            <?php if($book->available_copies > 0): ?>
                                <span class="text-success"><?php echo e($book->available_copies); ?> db</span>
                            <?php else: ?>
                                <span class="text-danger">Nem elérhető</span>
                            <?php endif; ?>
                        </p>
                    </div>
                    <div class="card-footer">
                        <div class="btn-group w-100" role="group">
                            <a href="<?php echo e(route('books.show', $book)); ?>" class="btn btn-outline-primary btn-sm">
                                <i class="bi bi-eye"></i> Megtekintés
                            </a>
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('update', $book)): ?>
                                <a href="<?php echo e(route('books.edit', $book)); ?>" class="btn btn-outline-secondary btn-sm">
                                    <i class="bi bi-pencil"></i> Szerkesztés
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
    
    <!-- Pagination -->
    <div class="d-flex justify-content-center">
        <?php echo e($books->withQueryString()->links()); ?>

    </div>
<?php else: ?>
    <div class="text-center py-5">
        <i class="bi bi-book display-1 text-muted"></i>
        <h3 class="text-muted mt-3">Nincs könyv</h3>
        <p class="text-muted">Még nem adtak hozzá könyvet a rendszerhez.</p>
        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create', App\Models\Book::class)): ?>
            <a href="<?php echo e(route('books.create')); ?>" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Első könyv hozzáadása
            </a>
        <?php endif; ?>
    </div>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/resources/views/books/index.blade.php ENDPATH**/ ?>