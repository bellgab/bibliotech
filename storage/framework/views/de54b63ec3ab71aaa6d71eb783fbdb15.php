<?php $__env->startSection('title', 'Admin Dashboard'); ?>

<?php $__env->startSection('content'); ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><i class="bi bi-speedometer2"></i> Admin Dashboard</h1>
    <div class="btn-group">
        <a href="<?php echo e(route('admin.analytics')); ?>" class="btn btn-outline-primary">
            <i class="bi bi-graph-up"></i> Analytics
        </a>
        <a href="<?php echo e(route('admin.system-status')); ?>" class="btn btn-outline-info">
            <i class="bi bi-activity"></i> System Status
        </a>
        <button type="button" class="btn btn-outline-success" onclick="testNotifications()">
            <i class="bi bi-bell"></i> Test Notifications
        </button>
    </div>
</div>

<!-- System Health Status -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="bi bi-shield-check"></i> System Health</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <?php $__currentLoopData = $systemHealth; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $component => $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="col-md-3 mb-2">
                            <div class="d-flex align-items-center">
                                <?php if($status): ?>
                                    <i class="bi bi-check-circle-fill text-success me-2"></i>
                                <?php else: ?>
                                    <i class="bi bi-x-circle-fill text-danger me-2"></i>
                                <?php endif; ?>
                                <span class="text-capitalize"><?php echo e(str_replace('_', ' ', $component)); ?></span>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Statistics -->
<div class="row mb-4">
    <div class="col-md-2">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <div class="display-4 text-primary mb-2">
                    <i class="bi bi-book-fill"></i>
                </div>
                <h3 class="mb-1"><?php echo e(number_format($stats['total_books'])); ?></h3>
                <p class="text-muted mb-0">Összes könyv</p>
                <small class="text-success">
                    <i class="bi bi-arrow-up"></i> +<?php echo e($trending['books_added_this_month']); ?> ez a hónap
                </small>
            </div>
        </div>
    </div>
    
    <div class="col-md-2">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <div class="display-4 text-info mb-2">
                    <i class="bi bi-people-fill"></i>
                </div>
                <h3 class="mb-1"><?php echo e(number_format($stats['total_users'])); ?></h3>
                <p class="text-muted mb-0">Felhasználó</p>
                <small class="text-success">
                    <i class="bi bi-arrow-up"></i> +<?php echo e($trending['new_users_this_month']); ?> ez a hónap
                </small>
            </div>
        </div>
    </div>
    
    <div class="col-md-2">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <div class="display-4 text-warning mb-2">
                    <i class="bi bi-bookmark-fill"></i>
                </div>
                <h3 class="mb-1"><?php echo e(number_format($stats['active_borrows'])); ?></h3>
                <p class="text-muted mb-0">Aktív kölcsönzés</p>
                <small class="text-info">
                    <i class="bi bi-arrow-up"></i> <?php echo e($trending['borrows_this_month']); ?> ez a hónap
                </small>
            </div>
        </div>
    </div>
    
    <div class="col-md-2">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <div class="display-4 text-danger mb-2">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                </div>
                <h3 class="mb-1"><?php echo e(number_format($stats['overdue_books'])); ?></h3>
                <p class="text-muted mb-0">Lejárt</p>
                <?php if($stats['overdue_books'] > 0): ?>
                    <small class="text-danger">
                        <i class="bi bi-bell"></i> Figyelmet igényel
                    </small>
                <?php else: ?>
                    <small class="text-success">
                        <i class="bi bi-check"></i> Minden rendben
                    </small>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-md-2">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <div class="display-4 text-secondary mb-2">
                    <i class="bi bi-star-fill"></i>
                </div>
                <h3 class="mb-1"><?php echo e(number_format($stats['total_reviews'])); ?></h3>
                <p class="text-muted mb-0">Értékelések</p>
                <small class="text-info">
                    <i class="bi bi-arrow-up"></i> +<?php echo e($trending['reviews_this_month']); ?> ez a hónap
                </small>
            </div>
        </div>
    </div>

    <div class="col-md-2">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <div class="display-4 text-warning mb-2">
                    <i class="bi bi-clock-fill"></i>
                </div>
                <h3 class="mb-1"><?php echo e(number_format($stats['pending_reviews'])); ?></h3>
                <p class="text-muted mb-0">Jóváhagyásra vár</p>
                <?php if($stats['pending_reviews'] > 0): ?>
                    <small class="text-warning">
                        <i class="bi bi-bell"></i> Figyelmet igényel
                    </small>
                <?php else: ?>
                    <small class="text-success">
                        <i class="bi bi-check"></i> Minden jóváhagyott
                    </small>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Notification Status -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="bi bi-bell"></i> Értesítési státusz</h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span>Közelgő határidők (3 nap):</span>
                    <span class="badge bg-warning"><?php echo e($notificationStats['upcoming_due']); ?></span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span>Lejárt kölcsönzések:</span>
                    <span class="badge bg-danger"><?php echo e($notificationStats['overdue']); ?></span>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <span><strong>Összes értesítendő:</strong></span>
                    <span class="badge bg-primary"><?php echo e($notificationStats['total_pending']); ?></span>
                </div>
                
                <?php if($notificationStats['total_pending'] > 0): ?>
                    <hr>
                    <button class="btn btn-primary btn-sm w-100" onclick="sendNotifications()">
                        <i class="bi bi-send"></i> Értesítések küldése
                    </button>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="bi bi-activity"></i> Havi aktivitás</h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6">
                        <h4 class="text-success"><?php echo e($trending['borrows_this_month']); ?></h4>
                        <small class="text-muted">Kölcsönzések</small>
                    </div>
                    <div class="col-6">
                        <h4 class="text-info"><?php echo e($trending['returns_this_month']); ?></h4>
                        <small class="text-muted">Visszahozások</small>
                    </div>
                </div>
                
                <?php
                    $returnRate = $trending['borrows_this_month'] > 0 
                        ? round(($trending['returns_this_month'] / $trending['borrows_this_month']) * 100, 1)
                        : 0;
                ?>
                
                <div class="progress mt-3" style="height: 8px;">
                    <div class="progress-bar bg-success" style="width: <?php echo e(min($returnRate, 100)); ?>%"></div>
                </div>
                <small class="text-muted">Visszahozási arány: <?php echo e($returnRate); ?>%</small>
            </div>
        </div>
    </div>
</div>

<!-- Reviews Management -->
<?php if($stats['pending_reviews'] > 0): ?>
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-clock-fill text-warning"></i> Jóváhagyásra váró értékelések</h5>
                <a href="<?php echo e(route('reviews.index', ['status' => 'pending'])); ?>" class="btn btn-outline-primary btn-sm">
                    <i class="bi bi-eye"></i> Összes megtekintése
                </a>
            </div>
            <div class="card-body">
                <?php $__currentLoopData = $pendingReviews; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $review): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="row align-items-center py-2 <?php echo e(!$loop->last ? 'border-bottom' : ''); ?>">
                        <div class="col-md-4">
                            <strong><?php echo e(Str::limit($review->book->title, 30)); ?></strong><br>
                            <small class="text-muted"><?php echo e($review->book->author->name); ?></small>
                        </div>
                        <div class="col-md-3">
                            <div class="d-flex align-items-center">
                                <?php for($i = 1; $i <= 5; $i++): ?>
                                    <i class="bi bi-star<?php echo e($i <= $review->rating ? '-fill' : ''); ?> text-warning"></i>
                                <?php endfor; ?>
                                <span class="ms-2"><?php echo e($review->rating); ?>/5</span>
                            </div>
                            <small class="text-muted"><?php echo e($review->user->name); ?></small>
                        </div>
                        <div class="col-md-3">
                            <small class="text-muted"><?php echo e(Str::limit($review->comment, 50)); ?></small><br>
                            <small class="text-muted"><?php echo e($review->created_at->format('Y.m.d. H:i')); ?></small>
                        </div>
                        <div class="col-md-2 text-end">
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-success" onclick="approveReview(<?php echo e($review->id); ?>)" title="Jóváhagyás">
                                    <i class="bi bi-check-lg"></i>
                                </button>
                                <a href="<?php echo e(route('reviews.show', $review)); ?>" class="btn btn-outline-primary" title="Megtekintés">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <button class="btn btn-outline-danger" onclick="deleteReview(<?php echo e($review->id); ?>)" title="Törlés">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                
                <?php if($stats['pending_reviews'] > count($pendingReviews)): ?>
                    <div class="text-center mt-3">
                        <small class="text-muted">És még <?php echo e($stats['pending_reviews'] - count($pendingReviews)); ?> értékelés...</small>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Review Statistics -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="bi bi-star-fill"></i> Értékelési statisztikák</h5>
            </div>
            <div class="card-body">
                <div class="row text-center mb-3">
                    <div class="col-6">
                        <h4 class="text-primary"><?php echo e(number_format($reviewStats['average_rating'], 1)); ?></h4>
                        <small class="text-muted">Átlagos értékelés</small>
                    </div>
                    <div class="col-6">
                        <h4 class="text-info"><?php echo e($reviewStats['reviewed_books_count']); ?></h4>
                        <small class="text-muted">Értékelt könyv</small>
                    </div>
                </div>
                
                <div class="mb-2">
                    <div class="d-flex justify-content-between">
                        <span>5 csillag</span>
                        <span><?php echo e($reviewStats['rating_distribution'][5] ?? 0); ?></span>
                    </div>
                    <div class="progress mb-2" style="height: 8px;">
                        <div class="progress-bar bg-success" style="width: <?php echo e($stats['total_reviews'] > 0 ? (($reviewStats['rating_distribution'][5] ?? 0) / $stats['total_reviews']) * 100 : 0); ?>%"></div>
                    </div>
                </div>
                
                <div class="mb-2">
                    <div class="d-flex justify-content-between">
                        <span>4 csillag</span>
                        <span><?php echo e($reviewStats['rating_distribution'][4] ?? 0); ?></span>
                    </div>
                    <div class="progress mb-2" style="height: 8px;">
                        <div class="progress-bar bg-info" style="width: <?php echo e($stats['total_reviews'] > 0 ? (($reviewStats['rating_distribution'][4] ?? 0) / $stats['total_reviews']) * 100 : 0); ?>%"></div>
                    </div>
                </div>
                
                <div class="mb-2">
                    <div class="d-flex justify-content-between">
                        <span>3 csillag</span>
                        <span><?php echo e($reviewStats['rating_distribution'][3] ?? 0); ?></span>
                    </div>
                    <div class="progress mb-2" style="height: 8px;">
                        <div class="progress-bar bg-warning" style="width: <?php echo e($stats['total_reviews'] > 0 ? (($reviewStats['rating_distribution'][3] ?? 0) / $stats['total_reviews']) * 100 : 0); ?>%"></div>
                    </div>
                </div>
                
                <div class="mb-2">
                    <div class="d-flex justify-content-between">
                        <span>2 csillag</span>
                        <span><?php echo e($reviewStats['rating_distribution'][2] ?? 0); ?></span>
                    </div>
                    <div class="progress mb-2" style="height: 8px;">
                        <div class="progress-bar bg-warning" style="width: <?php echo e($stats['total_reviews'] > 0 ? (($reviewStats['rating_distribution'][2] ?? 0) / $stats['total_reviews']) * 100 : 0); ?>%"></div>
                    </div>
                </div>
                
                <div class="mb-2">
                    <div class="d-flex justify-content-between">
                        <span>1 csillag</span>
                        <span><?php echo e($reviewStats['rating_distribution'][1] ?? 0); ?></span>
                    </div>
                    <div class="progress mb-2" style="height: 8px;">
                        <div class="progress-bar bg-danger" style="width: <?php echo e($stats['total_reviews'] > 0 ? (($reviewStats['rating_distribution'][1] ?? 0) / $stats['total_reviews']) * 100 : 0); ?>%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="bi bi-trophy-fill"></i> Legjobban értékelt könyvek</h5>
            </div>
            <div class="card-body">
                <?php $__currentLoopData = $topRatedBooks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $book): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="d-flex justify-content-between align-items-center py-2 <?php echo e(!$loop->last ? 'border-bottom' : ''); ?>">
                        <div>
                            <strong><?php echo e(Str::limit($book->title, 25)); ?></strong><br>
                            <small class="text-muted"><?php echo e($book->author->name); ?></small>
                        </div>
                        <div class="text-end">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-star-fill text-warning me-1"></i>
                                <span class="fw-bold"><?php echo e(number_format($book->average_rating, 1)); ?></span>
                            </div>
                            <small class="text-muted"><?php echo e($book->reviews_count); ?> értékelés</small>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    </div>
</div>

<!-- Popular Books and Active Users -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="bi bi-star-fill"></i> Legnépszerűbb könyvek</h5>
            </div>
            <div class="card-body">
                <?php $__currentLoopData = $popularBooks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $book): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="d-flex justify-content-between align-items-center py-2 <?php echo e(!$loop->last ? 'border-bottom' : ''); ?>">
                        <div>
                            <strong><?php echo e(Str::limit($book->title, 30)); ?></strong><br>
                            <small class="text-muted"><?php echo e($book->author->name); ?></small>
                        </div>
                        <span class="badge bg-primary"><?php echo e($book->borrows_count); ?> kölcsönzés</span>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="bi bi-people-fill"></i> Legaktívabb felhasználók</h5>
            </div>
            <div class="card-body">
                <?php $__currentLoopData = $activeUsers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="d-flex justify-content-between align-items-center py-2 <?php echo e(!$loop->last ? 'border-bottom' : ''); ?>">
                        <div>
                            <strong><?php echo e($user->name); ?></strong><br>
                            <small class="text-muted"><?php echo e($user->email); ?></small>
                        </div>
                        <span class="badge bg-info"><?php echo e($user->borrowed_books_count); ?> könyv</span>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    </div>
</div>

<!-- Recent Activities -->
<div class="row">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="bi bi-clock"></i> Legutóbbi kölcsönzések</h5>
            </div>
            <div class="card-body">
                <?php $__currentLoopData = $recentBorrows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $borrow): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="d-flex justify-content-between align-items-center py-2 <?php echo e(!$loop->last ? 'border-bottom' : ''); ?>">
                        <div>
                            <strong><?php echo e(Str::limit($borrow->book->title, 25)); ?></strong><br>
                            <small class="text-muted"><?php echo e($borrow->user->name); ?></small>
                        </div>
                        <div class="text-end">
                            <small class="text-muted"><?php echo e($borrow->borrowed_at->format('m.d. H:i')); ?></small><br>
                            <?php if($borrow->is_overdue && !$borrow->returned_at): ?>
                                <span class="badge bg-danger">Lejárt</span>
                            <?php elseif(!$borrow->returned_at): ?>
                                <span class="badge bg-warning">Aktív</span>
                            <?php else: ?>
                                <span class="badge bg-success">Visszahozva</span>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="bi bi-check-circle"></i> Legutóbbi visszahozások</h5>
            </div>
            <div class="card-body">
                <?php $__currentLoopData = $recentReturns; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $return): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="d-flex justify-content-between align-items-center py-2 <?php echo e(!$loop->last ? 'border-bottom' : ''); ?>">
                        <div>
                            <strong><?php echo e(Str::limit($return->book->title, 25)); ?></strong><br>
                            <small class="text-muted"><?php echo e($return->user->name); ?></small>
                        </div>
                        <div class="text-end">
                            <small class="text-muted"><?php echo e($return->returned_at->format('m.d. H:i')); ?></small><br>
                            <span class="badge bg-success">Visszahozva</span>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    </div>
</div>

<script>
function testNotifications() {
    fetch('<?php echo e(route("admin.test-notification")); ?>', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('success', 'Notification test completed successfully!');
        } else {
            showToast('error', 'Notification test failed: ' + data.message);
        }
    })
    .catch(error => {
        showToast('error', 'Error testing notifications: ' + error.message);
    });
}

function sendNotifications() {
    if (confirm('Are you sure you want to send all pending notifications?')) {
        // This would call the artisan command via a route
        showToast('info', 'Notification sending started. This may take a few moments...');
        
        // Simulate the process - in reality, you'd call an endpoint that runs the artisan command
        setTimeout(() => {
            showToast('success', 'Notifications sent successfully!');
            location.reload(); // Refresh to update counts
        }, 3000);
    }
}

function approveReview(reviewId) {
    if (confirm('Biztosan jóváhagyja ezt az értékelést?')) {
        fetch(`/reviews/${reviewId}/approve`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('success', 'Értékelés sikeresen jóváhagyva!');
                location.reload();
            } else {
                showToast('error', 'Hiba történt: ' + (data.message || 'Ismeretlen hiba'));
            }
        })
        .catch(error => {
            showToast('error', 'Hiba történt az értékelés jóváhagyása során: ' + error.message);
        });
    }
}

function deleteReview(reviewId) {
    if (confirm('Biztosan törli ezt az értékelést? Ez a művelet nem vonható vissza!')) {
        fetch(`/reviews/${reviewId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('success', 'Értékelés sikeresen törölve!');
                location.reload();
            } else {
                showToast('error', 'Hiba történt: ' + (data.message || 'Ismeretlen hiba'));
            }
        })
        .catch(error => {
            showToast('error', 'Hiba történt az értékelés törlése során: ' + error.message);
        });
    }
}

function showToast(type, message) {
    const toastHtml = `
        <div class="toast position-fixed top-0 end-0 m-3" role="alert">
            <div class="toast-body bg-${type === 'success' ? 'success' : type === 'error' ? 'danger' : 'info'} text-white">
                <i class="bi bi-${type === 'success' ? 'check-circle' : type === 'error' ? 'x-circle' : 'info-circle'}"></i>
                ${message}
            </div>
        </div>
    `;
    
    const toastElement = document.createElement('div');
    toastElement.innerHTML = toastHtml;
    document.body.appendChild(toastElement.firstElementChild);
    
    const toast = new bootstrap.Toast(toastElement.firstElementChild);
    toast.show();
    
    setTimeout(() => {
        toastElement.firstElementChild.remove();
    }, 5000);
}
</script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/resources/views/admin/dashboard.blade.php ENDPATH**/ ?>