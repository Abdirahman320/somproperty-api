<?php $__env->startSection('page-title','Register Asset'); ?>
<?php $__env->startSection('content'); ?>
<div class="card-header">
  <span class="card-title">Register New Asset</span>
  <a href="<?php echo e(route('owner.assets.index')); ?>" class="btn btn-outline btn-sm"><?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'arrow-left']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'arrow-left']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $attributes = $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $component = $__componentOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?> Back to Assets</a>
</div>

<div class="card max-w-lg">
  <form method="POST" action="<?php echo e(route('owner.assets.store')); ?>">
    <?php echo csrf_field(); ?>
    <div class="form-row">
      <div class="form-group"><label class="form-label">Property *</label>
        <select name="property_id" class="form-control" required>
          <option value="">— Select property —</option>
          <?php $__empty_1 = true; $__currentLoopData = $properties; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <option value="<?php echo e($p->id); ?>" <?php echo e(old('property_id')==$p->id?'selected':''); ?>><?php echo e($p->name); ?></option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <option value="" disabled>No properties — add one first</option>
          <?php endif; ?>
        </select>
      </div>
      <div class="form-group"><label class="form-label">Asset Name *</label>
        <input name="name" class="form-control" required value="<?php echo e(old('name')); ?>" placeholder="e.g. Rooftop AC Unit"></div>
    </div>
    <div class="form-row">
      <div class="form-group"><label class="form-label">Category *</label>
        <select name="category" class="form-control" required>
          <?php $__currentLoopData = ['mechanical','electrical','plumbing','electronic','furniture','vehicle','other']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($cat); ?>" <?php echo e(old('category')===$cat?'selected':''); ?>><?php echo e(ucfirst($cat)); ?></option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>
      <div class="form-group"><label class="form-label">Location</label>
        <input name="location" class="form-control" value="<?php echo e(old('location')); ?>" placeholder="e.g. Basement, Floor 3"></div>
    </div>
    <div class="form-row">
      <div class="form-group"><label class="form-label">Brand</label>
        <input name="brand" class="form-control" value="<?php echo e(old('brand')); ?>" placeholder="e.g. Samsung"></div>
      <div class="form-group"><label class="form-label">Model</label>
        <input name="model" class="form-control" value="<?php echo e(old('model')); ?>" placeholder="Model number"></div>
    </div>
    <div class="form-row">
      <div class="form-group"><label class="form-label">Serial Number</label>
        <input name="serial_number" class="form-control" value="<?php echo e(old('serial_number')); ?>"></div>
      <div class="form-group"><label class="form-label">Purchase Value</label>
        <input name="purchase_value" type="number" step="0.01" min="0" class="form-control" value="<?php echo e(old('purchase_value')); ?>" placeholder="0.00"></div>
    </div>
    <div class="form-row">
      <div class="form-group"><label class="form-label">Purchase Date</label>
        <input name="purchase_date" type="date" class="form-control" value="<?php echo e(old('purchase_date')); ?>"></div>
      <div class="form-group"><label class="form-label">Warranty Expires</label>
        <input name="warranty_expires_at" type="date" class="form-control" value="<?php echo e(old('warranty_expires_at')); ?>"></div>
    </div>
    <div class="form-actions">
      <a href="<?php echo e(route('owner.assets.index')); ?>" class="btn btn-outline">Cancel</a>
      <button type="submit" class="btn btn-primary">Register Asset</button>
    </div>
  </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.owner', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\abdirahman\Downloads\SOM_Property_Web_Fixed (1)\som_web\resources\views/owner/assets/create.blade.php ENDPATH**/ ?>