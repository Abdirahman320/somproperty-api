<?php $__env->startSection('page-title','Tenant Details'); ?>
<?php $__env->startSection('content'); ?>
<div class="card-header">
  <span class="card-title"><?php echo e($tenant->full_name); ?></span>
  <a href="<?php echo e(route('owner.tenants.index')); ?>" class="btn btn-outline btn-sm"><?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
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
<?php endif; ?> Back to Tenants</a>
</div>

<div class="grid-2 mb-4">
  <div class="card">
    <div class="card-title mb-4">Profile</div>
    <div class="flex flex-col gap-2 text-md">
      <div><span class="text-muted">Email:</span> <?php echo e($tenant->email); ?></div>
      <div><span class="text-muted">Phone:</span> <?php echo e($tenant->phone ?? '—'); ?></div>
      <div><span class="text-muted">National ID:</span> <?php echo e($tenant->national_id ?? '—'); ?></div>
      <div><span class="text-muted">Status:</span>
        <span class="badge badge-<?php echo e($tenant->status==='active'?'success':'danger'); ?>"><?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => ''.e($tenant->status==='active'?'check-circle':'alert').'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => ''.e($tenant->status==='active'?'check-circle':'alert').'']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $attributes = $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $component = $__componentOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?><?php echo e(ucfirst($tenant->status)); ?></span>
      </div>
    </div>
  </div>
  <div class="card">
    <div class="card-title mb-4">Active Contract</div>
    <?php if($tenant->activeContract): ?>
      <div class="flex flex-col gap-2 text-md">
        <div><span class="text-muted">Unit:</span>
          <?php echo e($tenant->activeContract->unit?->unit_number ?? '—'); ?>,
          <?php echo e($tenant->activeContract->unit?->property?->name ?? '—'); ?></div>
        <div><span class="text-muted">Monthly Rent:</span> $<?php echo e(number_format($tenant->activeContract->monthly_rent, 2)); ?></div>
        <div><span class="text-muted">Term:</span>
          <?php echo e($tenant->activeContract->start_date?->format('M j, Y')); ?> &ndash;
          <?php echo e($tenant->activeContract->end_date?->format('M j, Y')); ?></div>
        <div class="mt-1">
          <form method="POST" action="<?php echo e(route('owner.contracts.terminate', $tenant->activeContract)); ?>"
                onsubmit="return confirm('Terminate this contract? The unit will be marked vacant.')">
            <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
            <button class="btn btn-danger btn-xs">Terminate Contract</button>
          </form>
        </div>
      </div>
    <?php else: ?>
      <p class="text-muted text-md">No active contract.</p>
    <?php endif; ?>
  </div>
</div>

<div class="table-wrap table-stack">
  <div class="table-title">Recent Bills</div>
  <div class="table-scroll">
  <table>
    <thead><tr><th>Month</th><th>Total</th><th>Paid</th><th>Status</th></tr></thead>
    <tbody>
      <?php $__empty_1 = true; $__currentLoopData = $tenant->bills; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $b): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
      <tr>
        <td data-label="Month"><?php echo e($b->billing_month?->format('M Y') ?? '—'); ?></td>
        <td data-label="Total">$<?php echo e(number_format($b->total_amount, 2)); ?></td>
        <td data-label="Paid">$<?php echo e(number_format($b->amount_paid, 2)); ?></td>
        <td data-label="Status"><?php $bs = $b->status==='paid'?'success':($b->status==='overdue'?'danger':'warning'); $bsi = $b->status==='paid'?'check-circle':($b->status==='overdue'?'alert':'clock'); ?>
          <span class="badge badge-<?php echo e($bs); ?>"><?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => ''.e($bsi).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => ''.e($bsi).'']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $attributes = $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $component = $__componentOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?><?php echo e(ucfirst($b->status)); ?></span></td>
      </tr>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
      <tr class="table-empty"><td colspan="4">
        <div class="empty-state"><div class="empty-icon"><?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'receipt']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'receipt']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $attributes = $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $component = $__componentOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?></div><div class="empty-title">No bills recorded</div></div>
      </td></tr>
      <?php endif; ?>
    </tbody>
  </table>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.owner', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\abdirahman\Downloads\SOM_Property_Web_Fixed (1)\som_web\resources\views/owner/tenants/show.blade.php ENDPATH**/ ?>