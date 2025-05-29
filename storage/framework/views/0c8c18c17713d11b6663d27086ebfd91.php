<?php $__env->startSection('title', $book->title); ?>

<?php $__env->startSection('content'); ?>
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0"><?php echo e($book->title); ?></h4>
                <div>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('update', $book)): ?>
                        <a href="<?php echo e(route('books.edit', $book)); ?>" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-pencil"></i> Szerkesztés
                        </a>
                    <?php endif; ?>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('delete', $book)): ?>
                        <form method="POST" action="<?php echo e(route('books.destroy', $book)); ?>" 
                              class="d-inline" onsubmit="return confirm('Biztosan törli ezt a könyvet?')">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('DELETE'); ?>
                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                <i class="bi bi-trash"></i> Törlés
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th width="30%">Szerző:</th>
                                <td><?php echo e($book->author->name); ?></td>
                            </tr>
                            <tr>
                                <th>Kategória:</th>
                                <td><?php echo e($book->category->name); ?></td>
                            </tr>
                            <tr>
                                <th>ISBN:</th>
                                <td><?php echo e($book->isbn); ?></td>
                            </tr>
                            <?php if($book->published_year): ?>
                            <tr>
                                <th>Kiadás éve:</th>
                                <td><?php echo e($book->published_year); ?></td>
                            </tr>
                            <?php endif; ?>
                            <tr>
                                <th>Összes példány:</th>
                                <td><?php echo e($book->total_copies); ?> db</td>
                            </tr>
                            <tr>
                                <th>Elérhető:</th>
                                <td>
                                    <?php if($book->available_copies > 0): ?>
                                        <span class="badge bg-success"><?php echo e($book->available_copies); ?> db</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Nem elérhető</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <th>Kölcsönzött:</th>
                                <td><?php echo e($book->total_copies - $book->available_copies); ?> db</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <?php if($book->description): ?>
                            <h6>Leírás:</h6>
                            <p><?php echo e($book->description); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <!-- Action buttons -->
        <div class="card mb-3">
            <div class="card-header">
                <h6 class="mb-0">Műveletek</h6>
            </div>
            <div class="card-body">
                <?php if($book->available_copies > 0): ?>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create', App\Models\BookBorrowing::class)): ?>
                        <a href="<?php echo e(route('borrows.create', ['book_id' => $book->id])); ?>" 
                           class="btn btn-success w-100 mb-2">
                            <i class="bi bi-book"></i> Kölcsönzés
                        </a>
                    <?php endif; ?>
                <?php else: ?>
                    <button class="btn btn-secondary w-100 mb-2" disabled>
                        <i class="bi bi-x-circle"></i> Nem elérhető
                    </button>
                <?php endif; ?>
                
                <a href="<?php echo e(route('books.index')); ?>" class="btn btn-outline-secondary w-100">
                    <i class="bi bi-arrow-left"></i> Vissza a listához
                </a>
            </div>
        </div>
        
        <!-- Recent borrowings -->
        <?php if($book->borrows->count() > 0): ?>
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Legutóbbi kölcsönzések</h6>
                </div>
                <div class="card-body">
                    <?php $__currentLoopData = $book->borrows()->with('user')->latest()->limit(5)->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $borrow): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="d-flex justify-content-between align-items-center py-2 
                                    <?php echo e(!$loop->last ? 'border-bottom' : ''); ?>">
                            <div>
                                <small class="text-muted"><?php echo e($borrow->user->name); ?></small><br>
                                <small><?php echo e($borrow->borrowed_at->format('Y.m.d')); ?></small>
                            </div>
                            <div>
                                <?php if($borrow->returned_at): ?>
                                    <span class="badge bg-success">Visszahozva</span>
                                <?php else: ?>
                                    <span class="badge bg-warning">Kölcsönzött</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/resources/views/books/show.blade.php ENDPATH**/ ?>