<?php $__env->startComponent('mail::message'); ?>
# Könyv visszahozási emlékeztető

Kedves <?php echo e($user->name); ?>!

<?php if($isOverdue): ?>
⚠️ **FIGYELEM:** A kölcsönzött könyv visszahozási határideje **lejárt**!
<?php else: ?>
🔔 Emlékeztetjük, hogy a kölcsönzött könyv visszahozási határideje közeledik.
<?php endif; ?>

## Könyv adatai
**Cím:** <?php echo e($book->title); ?>  
**Szerző:** <?php echo e($book->author->name); ?>  
**Kategória:** <?php echo e($book->category->name); ?>


## Kölcsönzési információk
**Visszahozási határidő:** <?php echo e($dueDate->format('Y. m. d.')); ?>  
<?php if($isOverdue): ?>
**Késés:** <?php echo e(now()->diffInDays($dueDate)); ?> nap
<?php else: ?>
**Hátralévő idő:** <?php echo e(now()->diffInDays($dueDate)); ?> nap
<?php endif; ?>

<?php if($isOverdue): ?>
<?php $__env->startComponent('mail::panel'); ?>
**KÉSEDELMI DÍJ**

A könyv lejárt visszahozása miatt késedelmi díj számítható fel. Kérjük, hozza vissza a könyvet a lehető leghamarabb!
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>

<?php $__env->startComponent('mail::button', ['url' => route('books.show', $book)]); ?>
Könyv megtekintése
<?php echo $__env->renderComponent(); ?>

<?php if($isOverdue): ?>
<?php $__env->startComponent('mail::button', ['url' => route('dashboard'), 'color' => 'red']); ?>
Saját kölcsönzések
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>

---

**Elérhetőségeink:**
- 📞 Telefon: +36 1 234 5678
- 📧 Email: info@bibliotech.hu
- 🌐 Weboldal: <?php echo e(config('app.url')); ?>


Köszönjük együttműködését!

**<?php echo e(config('app.name')); ?> csapata**
<?php echo $__env->renderComponent(); ?>
<?php /**PATH /var/www/html/resources/views/emails/borrow-reminder.blade.php ENDPATH**/ ?>