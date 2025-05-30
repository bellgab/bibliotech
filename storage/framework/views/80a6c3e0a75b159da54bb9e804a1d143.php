<?php if($reviews->count() > 0): ?>
    <div class="row">
        <?php $__currentLoopData = $reviews; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $review): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100 <?php echo e(!$review->is_approved ? 'border-warning' : ''); ?>">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h6 class="card-title mb-0">
                                <a href="<?php echo e(route('books.show', $review->book)); ?>" class="text-decoration-none">
                                    <?php echo e(Str::limit($review->book->title, 30)); ?>

                                </a>
                            </h6>
                            <?php if(!$review->is_approved): ?>
                                <span class="badge bg-warning text-dark">Várakozik</span>
                            <?php else: ?>
                                <span class="badge bg-success">Jóváhagyott</span>
                            <?php endif; ?>
                        </div>
                        
                        <p class="text-muted small mb-2">
                            <i class="fas fa-user"></i> <?php echo e($review->user->name); ?>

                            <br>
                            <i class="fas fa-book"></i> <?php echo e($review->book->author->name); ?>

                        </p>
                        
                        <div class="mb-2">
                            <div class="text-warning">
                                <?php echo $review->stars_display; ?>

                            </div>
                        </div>
                        
                        <?php if($review->comment): ?>
                            <p class="card-text">
                                <?php echo e(Str::limit($review->comment, 100)); ?>

                            </p>
                        <?php endif; ?>
                        
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">
                                <?php echo e($review->created_at->format('Y.m.d H:i')); ?>

                            </small>
                            
                            <div class="btn-group" role="group">
                                <a href="<?php echo e(route('reviews.show', $review)); ?>" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye"></i>
                                </a>
                                
                                <?php if($review->canBeEditedBy(auth()->user())): ?>
                                    <a href="<?php echo e(route('reviews.edit', $review)); ?>" class="btn btn-sm btn-outline-secondary">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                <?php endif; ?>
                                
                                <?php if(auth()->user()->is_admin || auth()->user()->is_librarian): ?>
                                    <?php if(!$review->is_approved): ?>
                                        <form method="POST" action="<?php echo e(route('reviews.approve', $review)); ?>" class="d-inline">
                                            <?php echo csrf_field(); ?>
                                            <button type="submit" class="btn btn-sm btn-success" 
                                                    onclick="return confirm('Biztosan jóváhagyod ezt az értékelést?')">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                <?php endif; ?>
                                
                                <?php if($review->canBeEditedBy(auth()->user()) || auth()->user()->is_admin): ?>
                                    <form method="POST" action="<?php echo e(route('reviews.destroy', $review)); ?>" class="d-inline">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                onclick="return confirm('Biztosan törölni szeretnéd ezt az értékelést?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-center">
        <?php echo e($reviews->withQueryString()->links()); ?>

    </div>
<?php else: ?>
    <div class="text-center py-5">
        <i class="fas fa-star fa-3x text-muted mb-3"></i>
        <h4>Nincs értékelés</h4>
        <p class="text-muted">
            <?php if(request()->hasAny(['search', 'status', 'book_id'])): ?>
                Nincs a keresési feltételeknek megfelelő értékelés.
            <?php else: ?>
                Még nincs értékelés a rendszerben.
            <?php endif; ?>
        </p>
        <?php if(!request()->hasAny(['search', 'status', 'book_id'])): ?>
            <a href="<?php echo e(route('reviews.create')); ?>" class="btn btn-primary">
                <i class="fas fa-plus"></i> Első értékelés létrehozása
            </a>
        <?php endif; ?>
    </div>
<?php endif; ?>
<?php /**PATH /var/www/html/resources/views/reviews/_review-cards.blade.php ENDPATH**/ ?>