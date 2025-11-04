<?php $__env->startSection('title', 'Értékelés részletei'); ?>

<?php $__env->startSection('content'); ?>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Értékelés részletei</h4>
                    <?php if(!$review->is_approved): ?>
                        <span class="badge bg-warning text-dark">Jóváhagyásra vár</span>
                    <?php else: ?>
                        <span class="badge bg-success">Jóváhagyott</span>
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <!-- Book Information -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <?php if($review->book->cover_image): ?>
                                <img src="<?php echo e(asset('storage/' . $review->book->cover_image)); ?>" 
                                     alt="<?php echo e($review->book->title); ?>" class="img-fluid rounded">
                            <?php else: ?>
                                <div class="bg-light rounded d-flex align-items-center justify-content-center" 
                                     style="height: 200px;">
                                    <i class="fas fa-book fa-3x text-muted"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-9">
                            <h5>
                                <a href="<?php echo e(route('books.show', $review->book)); ?>" class="text-decoration-none">
                                    <?php echo e($review->book->title); ?>

                                </a>
                            </h5>
                            <p class="text-muted mb-2">
                                <i class="fas fa-user"></i> Szerző: <?php echo e($review->book->author->name); ?>

                            </p>
                            <p class="text-muted mb-2">
                                <i class="fas fa-tag"></i> Kategória: <?php echo e($review->book->category->name); ?>

                            </p>
                            <?php if($review->book->isbn): ?>
                                <p class="text-muted mb-2">
                                    <i class="fas fa-barcode"></i> ISBN: <?php echo e($review->book->isbn); ?>

                                </p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <hr>

                    <!-- Review Information -->
                    <div class="row">
                        <div class="col-md-6">
                            <h6><i class="fas fa-star text-warning"></i> Értékelés</h6>
                            <div class="mb-3">
                                <div class="text-warning fs-4">
                                    <?php echo $review->stars_display; ?>

                                </div>
                                <span class="text-muted"><?php echo e($review->rating); ?>/5 csillag</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6><i class="fas fa-user"></i> Értékelő</h6>
                            <p class="mb-3"><?php echo e($review->user->name); ?></p>
                        </div>
                    </div>

                    <?php if($review->comment): ?>
                        <div class="mb-3">
                            <h6><i class="fas fa-comment"></i> Megjegyzés</h6>
                            <div class="bg-light p-3 rounded">
                                <?php echo e($review->comment); ?>

                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Dates -->
                    <div class="row">
                        <div class="col-md-6">
                            <h6><i class="fas fa-calendar"></i> Létrehozva</h6>
                            <p class="mb-2"><?php echo e($review->created_at->format('Y. m. d. H:i')); ?></p>
                        </div>
                        <?php if($review->is_approved && $review->approved_at): ?>
                            <div class="col-md-6">
                                <h6><i class="fas fa-check-circle text-success"></i> Jóváhagyva</h6>
                                <p class="mb-2">
                                    <?php echo e($review->approved_at->format('Y. m. d. H:i')); ?>

                                    <?php if($review->approvedBy): ?>
                                        <br><small class="text-muted">Jóváhagyta: <?php echo e($review->approvedBy->name); ?></small>
                                    <?php endif; ?>
                                </p>
                            </div>
                        <?php endif; ?>
                    </div>

                    <?php if($review->updated_at && $review->updated_at != $review->created_at): ?>
                        <div class="mb-3">
                            <h6><i class="fas fa-edit"></i> Utoljára módosítva</h6>
                            <p class="mb-0"><?php echo e($review->updated_at->format('Y. m. d. H:i')); ?></p>
                        </div>
                    <?php endif; ?>

                    <!-- Action Buttons -->
                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <a href="<?php echo e(route('reviews.index')); ?>" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Vissza a listához
                        </a>
                        
                        <div class="btn-group" role="group">
                            <a href="<?php echo e(route('books.show', $review->book)); ?>" class="btn btn-outline-primary">
                                <i class="fas fa-book"></i> Könyv megtekintése
                            </a>
                            
                            <?php if($review->canBeEditedBy(auth()->user())): ?>
                                <a href="<?php echo e(route('reviews.edit', $review)); ?>" class="btn btn-outline-secondary">
                                    <i class="fas fa-edit"></i> Szerkesztés
                                </a>
                            <?php endif; ?>
                            
                            <?php if((auth()->user()->is_admin || auth()->user()->is_librarian) && !$review->is_approved): ?>
                                <form method="POST" action="<?php echo e(route('reviews.approve', $review)); ?>" class="d-inline">
                                    <?php echo csrf_field(); ?>
                                    <button type="submit" class="btn btn-success" 
                                            onclick="return confirm('Biztosan jóváhagyod ezt az értékelést?')">
                                        <i class="fas fa-check"></i> Jóváhagyás
                                    </button>
                                </form>
                            <?php endif; ?>
                            
                            <?php if($review->canBeEditedBy(auth()->user()) || auth()->user()->is_admin): ?>
                                <form method="POST" action="<?php echo e(route('reviews.destroy', $review)); ?>" class="d-inline">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="btn btn-outline-danger" 
                                            onclick="return confirm('Biztosan törölni szeretnéd ezt az értékelést?')">
                                        <i class="fas fa-trash"></i> Törlés
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/resources/views/reviews/show.blade.php ENDPATH**/ ?>