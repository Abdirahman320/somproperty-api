<?php $__env->startSection('page-title','Settings'); ?>
<?php $__env->startSection('content'); ?>
<div class="grid-2">
  <div class="card">
    <div class="card-title mb-4">Account Settings</div>
    <form method="POST" action="<?php echo e(route('owner.settings.update')); ?>">
      <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
      <div class="form-group"><label class="form-label">Company Name</label><input name="company_name" class="form-control" value="<?php echo e($owner->company_name); ?>"></div>
      <div class="form-group"><label class="form-label">Phone</label><input name="phone" class="form-control" value="<?php echo e($owner->phone); ?>"></div>
      <div class="form-group"><label class="form-label">Timezone</label>
        <select name="timezone" class="form-control">
          <?php $__currentLoopData = timezone_identifiers_list(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tz): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($tz); ?>" <?php echo e($owner->timezone===$tz?'selected':''); ?>><?php echo e($tz); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>
      <button type="submit" class="btn btn-primary">Save Settings</button>
    </form>
  </div>
  <div class="card">
    <div class="card-title mb-4">Gmail SMTP Configuration</div>
    <div class="alert alert-info mb-4"><?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'info']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'info']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $attributes = $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $component = $__componentOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?><div class="alert-body">
      Use a Gmail App Password (not your account password).<br>
      Google Account &rsaquo; Security &rsaquo; 2-Step Verification &rsaquo; App Passwords
    </div></div>
    <form method="POST" action="<?php echo e(route('owner.settings.update')); ?>">
      <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
      <div class="form-group"><label class="form-label">Gmail Address</label><input name="smtp_user" type="email" class="form-control" value="<?php echo e($owner->smtp_user); ?>" placeholder="you@gmail.com"></div>
      <div class="form-group"><label class="form-label">App Password</label><input name="smtp_pass" type="password" class="form-control" placeholder="Leave blank to keep current"></div>
      <div class="form-row">
        <div class="form-group"><label class="form-label">SMTP Host</label><input name="smtp_host" class="form-control" value="<?php echo e($owner->smtp_host ?? 'smtp.gmail.com'); ?>"></div>
        <div class="form-group"><label class="form-label">Port</label><input name="smtp_port" type="number" class="form-control" value="<?php echo e($owner->smtp_port ?? 587); ?>"></div>
      </div>
      <button type="submit" class="btn btn-primary">Save Gmail Config</button>
      <?php if($owner->gmail_configured): ?><span class="badge badge-success ml-2"><?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'check-circle']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'check-circle']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $attributes = $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $component = $__componentOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?> Gmail configured</span><?php endif; ?>
    </form>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.owner', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\abdirahman\Downloads\SOM_Property_Web_Fixed (1)\som_web\resources\views/owner/settings/index.blade.php ENDPATH**/ ?>