<?php $__env->startSection('page-title','My Complaints'); ?>
<?php $__env->startSection('content'); ?>
<div class="card-header">
  <span class="card-title">My Complaints</span>
  <button class="btn btn-primary btn-sm" data-toggle="#newComplaintForm"><?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'plus']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'plus']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $attributes = $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $component = $__componentOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?> New Complaint</button>
</div>
<div id="newComplaintForm" class="card mb-4 d-none">
  <div class="card-title mb-4">Submit New Complaint</div>
  <form method="POST" action="<?php echo e(route('tenant.complaints.store')); ?>">
    <?php echo csrf_field(); ?>
    <div class="form-group"><label class="form-label">Title</label><input name="title" class="form-control" required placeholder="Brief description"></div>
    <div class="form-row">
      <div class="form-group"><label class="form-label">Category</label>
        <select name="category" class="form-control"><option value="plumbing">Plumbing</option><option value="electrical">Electrical</option><option value="structural">Structural</option><option value="noise">Noise</option><option value="cleaning">Cleaning</option><option value="furniture">Furniture</option><option value="other">Other</option></select>
      </div>
      <div class="form-group"><label class="form-label">Priority</label>
        <select name="priority" class="form-control"><option value="low">Low</option><option value="medium">Medium</option><option value="high">High</option><option value="emergency">Emergency</option></select>
      </div>
    </div>
    <div class="form-group"><label class="form-label">Description</label><textarea name="description" class="form-control" rows="4" required placeholder="Describe the issue in detail..."></textarea></div>
    <div class="form-actions">
      <button type="button" class="btn btn-outline" data-hide="#newComplaintForm">Cancel</button>
      <button type="submit" class="btn btn-primary">Submit Complaint</button>
    </div>
  </form>
</div>
<div class="notice-list">
<?php $__empty_1 = true; $__currentLoopData = $complaints; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
<?php $st = $c->status==='open'?'danger':($c->status==='resolved'?'success':'warning'); $sti = $c->status==='open'?'alert':($c->status==='resolved'?'check-circle':'clock'); ?>
<div class="card">
  <div class="flex items-center gap-2 flex-wrap mb-2">
    <b><?php echo e($c->title); ?></b>
    <span class="badge badge-gray"><?php echo e(ucfirst($c->category)); ?></span>
    <span class="badge badge-<?php echo e($st); ?>"><?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => ''.e($sti).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => ''.e($sti).'']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $attributes = $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $component = $__componentOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?><?php echo e(ucfirst(str_replace('_',' ',$c->status))); ?></span>
    <span class="text-xs text-muted nowrap ml-auto"><?php echo e($c->ticket_number); ?></span>
  </div>
  <?php if($c->replies->count()): ?>
  <div class="thread mt-2">
    <?php $__currentLoopData = $c->replies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="bubble <?php echo e($r->sender_type==='owner'?'is-staff':''); ?>">
      <div class="bubble-meta"><b class="text-default"><?php echo e($r->sender_type==='owner'?'Management':'You'); ?></b></div>
      <?php echo e($r->message); ?>

    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
  </div>
  <?php endif; ?>
  <div class="text-xs text-muted mt-2"><?php echo e($c->created_at->format('M j, Y')); ?></div>
</div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
<div class="empty-state">
  <div class="empty-icon"><?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'clipboard']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'clipboard']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $attributes = $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $component = $__componentOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?></div>
  <div class="empty-title">No complaints submitted yet</div>
  <div class="empty-text">Use the button above to report an issue.</div>
</div>
<?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.tenant', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\abdirahman\Downloads\SOM_Property_Web_Fixed (1)\som_web\resources\views/tenant/complaints/index.blade.php ENDPATH**/ ?>