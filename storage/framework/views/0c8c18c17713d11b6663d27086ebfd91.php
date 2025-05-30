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
                            <?php if($book->reviews_count > 0): ?>
                            <tr>
                                <th>Értékelés:</th>
                                <td>
                                    <div class="text-warning">
                                        <?php echo $book->stars_display; ?>

                                    </div>
                                    <small class="text-muted">
                                        <?php echo e(number_format($book->average_rating, 1)); ?>/5 
                                        (<?php echo e($book->reviews_count); ?> értékelés)
                                    </small>
                                </td>
                            </tr>
                            <?php endif; ?>
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

        <!-- Reviews Section -->
        <div class="card mt-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Értékelések</h5>
                <?php if(auth()->guard()->check()): ?>
                    <?php
                        $userReview = $book->reviews()->where('user_id', auth()->id())->first();
                    ?>
                    <?php if(!$userReview): ?>
                        <a href="<?php echo e(route('reviews.create', ['book_id' => $book->id])); ?>" 
                           class="btn btn-sm btn-primary">
                            <i class="fas fa-star"></i> Értékelés írása
                        </a>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
            <div class="card-body">
                <?php
                    $approvedReviews = $book->approvedReviews()->with('user')->latest()->get();
                ?>
                
                <?php if($approvedReviews->count() > 0): ?>
                    <?php $__currentLoopData = $approvedReviews; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $review): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="review-item <?php echo e(!$loop->last ? 'border-bottom' : ''); ?> pb-3 mb-3">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <div class="d-flex align-items-center mb-2">
                                        <strong class="me-3"><?php echo e($review->user->name); ?></strong>
                                        <div class="text-warning me-2">
                                            <?php echo $review->stars_display; ?>

                                        </div>
                                        <small class="text-muted">
                                            <?php echo e($review->created_at->format('Y.m.d')); ?>

                                        </small>
                                    </div>
                                    <?php if($review->comment): ?>
                                        <p class="mb-0"><?php echo e($review->comment); ?></p>
                                    <?php endif; ?>
                                </div>
                                <?php if($review->canBeEditedBy(auth()->user())): ?>
                                    <div class="ms-3">
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" 
                                                    type="button" data-bs-toggle="dropdown">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li>
                                                    <a class="dropdown-item" href="<?php echo e(route('reviews.edit', $review)); ?>">
                                                        <i class="fas fa-edit"></i> Szerkesztés
                                                    </a>
                                                </li>
                                                <li>
                                                    <form method="POST" action="<?php echo e(route('reviews.destroy', $review)); ?>" 
                                                          class="d-inline">
                                                        <?php echo csrf_field(); ?>
                                                        <?php echo method_field('DELETE'); ?>
                                                        <button type="submit" class="dropdown-item text-danger"
                                                                onclick="return confirm('Biztosan törölni szeretnéd ezt az értékelést?')">
                                                            <i class="fas fa-trash"></i> Törlés
                                                        </button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    
                    <?php if($approvedReviews->count() >= 5): ?>
                        <div class="text-center">
                            <a href="<?php echo e(route('reviews.index', ['book_id' => $book->id])); ?>" 
                               class="btn btn-outline-primary">
                                <i class="fas fa-eye"></i> Összes értékelés megtekintése
                            </a>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="text-center py-4">
                        <i class="fas fa-star fa-2x text-muted mb-3"></i>
                        <p class="text-muted mb-0">Ez a könyv még nem rendelkezik értékeléssel.</p>
                        <?php if(auth()->guard()->check()): ?>
                            <?php if(!$book->reviews()->where('user_id', auth()->id())->exists()): ?>
                                <p class="text-muted">Legyél te az első, aki értékeli!</p>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
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
                
                <!-- QR Code Actions -->
                <div class="dropdown w-100 mb-2">
                    <button class="btn btn-outline-primary dropdown-toggle w-100" type="button" 
                            data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-qr-code"></i> QR Kód
                    </button>
                    <ul class="dropdown-menu w-100">
                        <li>
                            <a class="dropdown-item" href="<?php echo e(route('books.qr', $book)); ?>" target="_blank">
                                <i class="bi bi-eye"></i> QR kód megtekintése
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="<?php echo e(route('qr.book.info', $book)); ?>" target="_blank">
                                <i class="bi bi-phone"></i> Mobil nézet
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item" href="#" onclick="copyQrUrl('<?php echo e(route('qr.book.info', $book)); ?>')">
                                <i class="bi bi-clipboard"></i> Link másolása
                            </a>
                        </li>
                    </ul>
                </div>
                
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

<script>
function copyQrUrl(url) {
    navigator.clipboard.writeText(url).then(function() {
        // Success feedback
        const toast = document.createElement('div');
        toast.className = 'toast position-fixed top-0 end-0 m-3';
        toast.innerHTML = `
            <div class="toast-body bg-success text-white">
                <i class="bi bi-check-circle"></i> QR link vágólapra másolva!
            </div>
        `;
        document.body.appendChild(toast);
        
        const bsToast = new bootstrap.Toast(toast);
        bsToast.show();
        
        setTimeout(() => {
            toast.remove();
        }, 3000);
    }).catch(function(err) {
        alert('Hiba történt a másolás során: ' + err);
    });
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/resources/views/books/show.blade.php ENDPATH**/ ?>