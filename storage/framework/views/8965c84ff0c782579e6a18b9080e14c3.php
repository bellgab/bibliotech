<?php $__env->startSection('title', 'Értékelés szerkesztése'); ?>

<?php $__env->startSection('content'); ?>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Értékelés szerkesztése</h4>
                </div>
                <div class="card-body">
                    <!-- Book Information -->
                    <div class="alert alert-info mb-4">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-book fa-2x me-3"></i>
                            <div>
                                <h6 class="mb-1"><?php echo e($review->book->title); ?></h6>
                                <p class="mb-0 text-muted"><?php echo e($review->book->author->name); ?></p>
                            </div>
                        </div>
                    </div>

                    <form method="POST" action="<?php echo e(route('reviews.update', $review)); ?>">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('PUT'); ?>

                        <div class="mb-3">
                            <label for="rating" class="form-label">Értékelés <span class="text-danger">*</span></label>
                            <div class="rating-input">
                                <?php for($i = 1; $i <= 5; $i++): ?>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="rating" 
                                               id="rating<?php echo e($i); ?>" value="<?php echo e($i); ?>" 
                                               <?php echo e((old('rating', $review->rating)) == $i ? 'checked' : ''); ?> required>
                                        <label class="form-check-label" for="rating<?php echo e($i); ?>">
                                            <?php echo e($i); ?> csillag
                                            <?php if($i == 1): ?> (Rossz) <?php endif; ?>
                                            <?php if($i == 2): ?> (Gyenge) <?php endif; ?>
                                            <?php if($i == 3): ?> (Átlagos) <?php endif; ?>
                                            <?php if($i == 4): ?> (Jó) <?php endif; ?>
                                            <?php if($i == 5): ?> (Kiváló) <?php endif; ?>
                                        </label>
                                    </div>
                                <?php endfor; ?>
                            </div>
                            <?php $__errorArgs = ['rating'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="text-danger small"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            
                            <!-- Visual star rating display -->
                            <div class="mt-2">
                                <span class="rating-display text-warning fs-4">
                                    <i class="far fa-star" data-rating="1"></i>
                                    <i class="far fa-star" data-rating="2"></i>
                                    <i class="far fa-star" data-rating="3"></i>
                                    <i class="far fa-star" data-rating="4"></i>
                                    <i class="far fa-star" data-rating="5"></i>
                                </span>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="comment" class="form-label">Megjegyzés</label>
                            <textarea class="form-control <?php $__errorArgs = ['comment'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                      id="comment" name="comment" rows="5" 
                                      placeholder="Írj részletes értékelést a könyvről..."><?php echo e(old('comment', $review->comment)); ?></textarea>
                            <div class="form-text">Minimum 10, maximum 1000 karakter.</div>
                            <?php $__errorArgs = ['comment'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <?php if($review->is_approved): ?>
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle"></i>
                                <strong>Figyelem:</strong> Ez az értékelés már jóvá lett hagyva. A módosítás után újra moderációra kerül.
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i>
                                <strong>Info:</strong> Az értékelésed még jóváhagyásra vár. A módosítások mentése után továbbra is moderációra fog várni.
                            </div>
                        <?php endif; ?>

                        <div class="d-flex justify-content-between">
                            <div>
                                <a href="<?php echo e(route('reviews.show', $review)); ?>" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Vissza
                                </a>
                                <a href="<?php echo e(route('reviews.index')); ?>" class="btn btn-outline-secondary">
                                    <i class="fas fa-list"></i> Lista
                                </a>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Módosítások mentése
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Review History -->
            <div class="card mt-4">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-history"></i> Értékelés információk</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <strong>Létrehozva:</strong> <?php echo e($review->created_at->format('Y. m. d. H:i')); ?>

                        </div>
                        <?php if($review->updated_at && $review->updated_at != $review->created_at): ?>
                            <div class="col-md-6">
                                <strong>Utoljára módosítva:</strong> <?php echo e($review->updated_at->format('Y. m. d. H:i')); ?>

                            </div>
                        <?php endif; ?>
                    </div>
                    <?php if($review->is_approved && $review->approved_at): ?>
                        <div class="row mt-2">
                            <div class="col-md-6">
                                <strong>Jóváhagyva:</strong> <?php echo e($review->approved_at->format('Y. m. d. H:i')); ?>

                            </div>
                            <?php if($review->approvedBy): ?>
                                <div class="col-md-6">
                                    <strong>Jóváhagyta:</strong> <?php echo e($review->approvedBy->name); ?>

                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const ratingInputs = document.querySelectorAll('input[name="rating"]');
    const ratingStars = document.querySelectorAll('.rating-display i');
    
    function updateStarDisplay(rating) {
        ratingStars.forEach((star, index) => {
            if (index < rating) {
                star.className = 'fas fa-star';
            } else {
                star.className = 'far fa-star';
            }
        });
    }
    
    // Handle radio button changes
    ratingInputs.forEach(input => {
        input.addEventListener('change', function() {
            updateStarDisplay(parseInt(this.value));
        });
    });
    
    // Handle star clicks
    ratingStars.forEach((star, index) => {
        star.addEventListener('click', function() {
            const rating = index + 1;
            document.getElementById('rating' + rating).checked = true;
            updateStarDisplay(rating);
        });
        
        star.style.cursor = 'pointer';
    });
    
    // Initialize with current value
    const checkedRating = document.querySelector('input[name="rating"]:checked');
    if (checkedRating) {
        updateStarDisplay(parseInt(checkedRating.value));
    }
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/resources/views/reviews/edit.blade.php ENDPATH**/ ?>