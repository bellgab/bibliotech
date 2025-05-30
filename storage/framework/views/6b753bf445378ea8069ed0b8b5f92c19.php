<?php $__env->startSection('title', 'Új értékelés'); ?>

<?php $__env->startSection('content'); ?>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Új könyv értékelés</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="<?php echo e(route('reviews.store')); ?>">
                        <?php echo csrf_field(); ?>
                        
                        <div class="mb-3">
                            <label for="book_id" class="form-label">Könyv <span class="text-danger">*</span></label>
                            <?php if($book): ?>
                                <div class="card">
                                    <div class="card-body p-3">
                                        <h6 class="mb-1"><?php echo e($book->title); ?></h6>
                                        <p class="text-muted mb-0"><?php echo e($book->author->name); ?></p>
                                    </div>
                                </div>
                                <input type="hidden" name="book_id" value="<?php echo e($book->id); ?>">
                            <?php else: ?>
                                <select class="form-select <?php $__errorArgs = ['book_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                        id="book_id" name="book_id" required>
                                    <option value="">Válassz könyvet...</option>
                                    <?php $__currentLoopData = $books; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bookOption): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($bookOption->id); ?>" 
                                                <?php echo e(old('book_id') == $bookOption->id ? 'selected' : ''); ?>>
                                            <?php echo e($bookOption->title); ?> - <?php echo e($bookOption->author->name); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <?php $__errorArgs = ['book_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            <?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label for="rating" class="form-label">Értékelés <span class="text-danger">*</span></label>
                            <div class="rating-input">
                                <?php for($i = 1; $i <= 5; $i++): ?>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="rating" 
                                               id="rating<?php echo e($i); ?>" value="<?php echo e($i); ?>" 
                                               <?php echo e(old('rating') == $i ? 'checked' : ''); ?> required>
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
                                      placeholder="Írj részletes értékelést a könyvről..."><?php echo e(old('comment')); ?></textarea>
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

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            <strong>Fontos:</strong> Az értékelésed moderációra kerül, és csak jóváhagyás után jelenik meg nyilvánosan.
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="<?php echo e(route('reviews.index')); ?>" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Vissza
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Értékelés mentése
                            </button>
                        </div>
                    </form>
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

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/resources/views/reviews/create.blade.php ENDPATH**/ ?>