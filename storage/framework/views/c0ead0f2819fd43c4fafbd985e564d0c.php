<!DOCTYPE html>
<html>
<head>
    <title>CSRF Test</title>
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
</head>
<body>
    <h1>CSRF Test Form</h1>
    
    <?php if(session('success')): ?>
        <div style="color: green;"><?php echo e(session('success')); ?></div>
    <?php endif; ?>
    
    <?php if($errors->any()): ?>
        <div style="color: red;">
            <ul>
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>
    
    <form method="POST" action="<?php echo e(route('debug.csrf.post')); ?>">
        <?php echo csrf_field(); ?>
        <input type="text" name="test_field" placeholder="Enter test text" required>
        <button type="submit">Submit</button>
    </form>
    
    <hr>
    
    <h2>Session Info</h2>
    <ul>
        <li>CSRF Token: <?php echo e(csrf_token()); ?></li>
        <li>Session ID: <?php echo e(session()->getId()); ?></li>
        <li>Session Driver: <?php echo e(config('session.driver')); ?></li>
    </ul>
</body>
</html>
<?php /**PATH /var/www/html/resources/views/debug/csrf-test.blade.php ENDPATH**/ ?>