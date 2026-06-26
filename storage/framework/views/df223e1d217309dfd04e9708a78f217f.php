<?php $__env->startSection('page-title','Property Owners'); ?>
<?php $__env->startSection('content'); ?>
<div class="card-header">
  <span class="card-title">All Owners (<?php echo e($owners->total()); ?>)</span>
  <a href="<?php echo e(route('admin.owners.create')); ?>" class="btn btn-primary btn-sm"><?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
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
<?php endif; ?> Create Owner</a>
</div>
<div class="table-wrap table-stack">
 <div class="table-scroll">
  <table id="ownersTable">
    <thead><tr><th data-sort>Owner</th><th>Email</th><th>Plan</th><th>Max Apts</th><th>Used</th><th>Status</th><th>Joined</th><th>Actions</th></tr></thead>
    <tbody>
      <?php $__currentLoopData = $owners; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $o): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
      <tr>
        <td data-label="Owner"><b><?php echo e($o->full_name); ?></b><div class="text-xs text-muted"><?php echo e($o->company_name); ?></div></td>
        <td data-label="Email" class="text-sm"><?php echo e($o->email); ?></td>
        <td data-label="Plan"><span class="badge badge-info"><?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'package']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'package']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $attributes = $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $component = $__componentOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?><?php echo e($o->plan?->name ?? '—'); ?></span></td>
        <td data-label="Max Apts" class="text-center"><?php echo e($o->max_apartments); ?></td>
        <td data-label="Used" class="text-center"><?php echo e($o->usedApartments()); ?></td>
        <td data-label="Status"><?php $os = $o->status==='active'?'success':($o->status==='trial'?'warning':'danger'); $osi = $o->status==='active'?'check-circle':($o->status==='trial'?'clock':'alert'); ?>
          <span class="badge badge-<?php echo e($os); ?>"><?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => ''.e($osi).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => ''.e($osi).'']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $attributes = $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $component = $__componentOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?><?php echo e(ucfirst($o->status)); ?></span></td>
        <td data-label="Joined" class="text-sm text-muted"><?php echo e($o->created_at->format('M j, Y')); ?></td>
        <td data-label="Actions">
          <?php if($o->status==='active'): ?>
            <form method="POST" action="<?php echo e(route('admin.owners.suspend',$o)); ?>" class="d-inline"><?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
              <button class="btn btn-outline btn-xs">Suspend</button></form>
          <?php else: ?>
            <form method="POST" action="<?php echo e(route('admin.owners.activate',$o)); ?>" class="d-inline"><?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
              <button class="btn btn-gold btn-xs">Activate</button></form>
          <?php endif; ?>
        </td>
      </tr>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </tbody>
  </table>
 </div>
</div>
<?php echo e($owners->links()); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startPush('scripts'); ?><script>initSortableTable('ownersTable');filterTable('globalSearch','ownersTable');</script><?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\abdirahman\Downloads\SOM_Property_Web_Fixed (1)\som_web\resources\views/admin/owners/index.blade.php ENDPATH**/ ?>