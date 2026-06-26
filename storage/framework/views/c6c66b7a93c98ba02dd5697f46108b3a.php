<?php $__env->startSection('page-title','Plans & Pricing'); ?>
<?php $__env->startSection('content'); ?>

<div class="card-header">
  <span class="card-title">Subscription Plans</span>
  <button class="btn btn-primary btn-sm" onclick="Modal.open('Add New Plan', document.getElementById('add-plan-tpl').innerHTML)"><?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
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
<?php endif; ?> Add Plan</button>
</div>


<div class="grid-5 mb-5">
  <?php $__currentLoopData = $plans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
  <div class="stat-card">
    <div class="stat-label"><?php echo e($p->name); ?></div>
    <div class="stat-value sm">$<?php echo e(number_format($p->price_monthly,0)); ?><span class="text-md text-muted fw-500">/mo</span></div>
    <div class="text-sm text-muted mt-1">up to <?php echo e($p->max_apartments); ?> units</div>
    <div class="flex gap-2 flex-wrap mt-2">
      <span class="badge badge-<?php echo e($p->is_active?'success':'gray'); ?>"><?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => ''.e($p->is_active?'check-circle':'x').'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => ''.e($p->is_active?'check-circle':'x').'']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $attributes = $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $component = $__componentOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?><?php echo e($p->is_active?'Active':'Inactive'); ?></span>
      <span class="badge badge-gray"><?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'briefcase']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'briefcase']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $attributes = $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $component = $__componentOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?><?php echo e($p->owners_count); ?> owner<?php echo e($p->owners_count==1?'':'s'); ?></span>
    </div>
  </div>
  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>


<div class="table-wrap table-stack">
 <div class="table-scroll">
  <table>
    <thead>
      <tr><th>Plan</th><th>Capacity</th><th>Price / mo</th><th>Active Owners</th><th>MRR</th><th>Edit (admin only)</th></tr>
    </thead>
    <tbody>
      <?php $__currentLoopData = $plans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
      <tr>
        <td data-label="Plan"><b><?php echo e($p->name); ?></b><div class="text-xs text-muted"><?php echo e($p->slug); ?></div></td>
        <td data-label="Capacity">up to <?php echo e($p->max_apartments); ?> units</td>
        <td data-label="Price / mo"><b>$<?php echo e(number_format($p->price_monthly,2)); ?></b></td>
        <td data-label="Active Owners"><?php echo e($p->owners_count); ?></td>
        <td data-label="MRR"><b>$<?php echo e(number_format($p->owners_count * $p->price_monthly,0)); ?></b></td>
        <td data-label="Edit">
          <form method="POST" action="<?php echo e(route('admin.plans.update',$p)); ?>" class="flex gap-2 items-center flex-wrap">
            <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
            <input name="name" value="<?php echo e($p->name); ?>" class="form-control input-narrow" title="Plan name" aria-label="Plan name">
            <input name="price_monthly" type="number" step="0.01" min="0" value="<?php echo e($p->price_monthly); ?>" class="form-control input-narrow" title="Price / month" aria-label="Price per month">
            <input name="max_apartments" type="number" min="1" value="<?php echo e($p->max_apartments); ?>" class="form-control input-narrow" title="Max apartments (cap)" aria-label="Max apartments">
            <label class="flex items-center gap-1 text-xs text-muted">
              <input type="checkbox" name="is_active" value="1" <?php echo e($p->is_active?'checked':''); ?>> Active
            </label>
            <button class="btn btn-primary btn-xs" type="submit">Save</button>
          </form>
        </td>
      </tr>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </tbody>
  </table>
 </div>
</div>
<p class="text-sm text-muted mt-3">Capacity is the enforced limit: an owner on a plan can hold up to and including the stated number of apartments. Only administrators can create or change plans.</p>


<template id="add-plan-tpl">
  <form method="POST" action="<?php echo e(route('admin.plans.store')); ?>">
    <?php echo csrf_field(); ?>
    <div class="form-group"><label class="form-label">Plan Name</label>
      <input name="name" class="form-control" required placeholder="e.g. Enterprise"></div>
    <div class="form-group"><label class="form-label">Slug (optional)</label>
      <input name="slug" class="form-control" placeholder="auto-generated from name"></div>
    <div class="form-row">
      <div class="form-group"><label class="form-label">Price / month ($)</label>
        <input name="price_monthly" type="number" step="0.01" min="0" class="form-control" required placeholder="200"></div>
      <div class="form-group"><label class="form-label">Max Apartments (cap)</label>
        <input name="max_apartments" type="number" min="1" class="form-control" required placeholder="e.g. 300 for up to 300 units"></div>
    </div>
    <div class="form-actions">
      <button type="button" class="btn btn-outline" onclick="Modal.close()">Cancel</button>
      <button type="submit" class="btn btn-primary">Create Plan</button>
    </div>
  </form>
</template>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\abdirahman\Downloads\SOM_Property_Web_Fixed (1)\som_web\resources\views/admin/plans/index.blade.php ENDPATH**/ ?>