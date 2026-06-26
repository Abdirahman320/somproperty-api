<?php $__env->startSection('page-title','Bill Detail'); ?>
<?php $__env->startSection('content'); ?>
<div class="card-header">
  <span class="card-title">Bill — <?php echo e($bill->tenant?->full_name ?? '—'); ?> (<?php echo e($bill->billing_month->format('F Y')); ?>)</span>
  <div class="flex gap-2 flex-wrap">
    <a href="<?php echo e(route('owner.billing.bills.pdf', $bill)); ?>" class="btn btn-outline btn-sm"><?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'file-text']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'file-text']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $attributes = $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $component = $__componentOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?> PDF</a>
    <?php if($bill->status !== 'paid'): ?>
      <a href="<?php echo e(route('owner.billing.bills.pay', $bill)); ?>" class="btn btn-gold btn-sm">Record Payment</a>
    <?php endif; ?>
    <a href="<?php echo e(route('owner.billing.index')); ?>" class="btn btn-outline btn-sm"><?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
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
<?php endif; ?> Back</a>
  </div>
</div>

<div class="grid-2 mb-4">
  <div class="card">
    <div class="card-title mb-4">Charges</div>
    <div class="kv-row"><span class="text-muted">Rent</span><span>$<?php echo e(number_format($bill->rent_amount,2)); ?></span></div>
    <div class="kv-row"><span class="text-muted">Water (<?php echo e(rtrim(rtrim(number_format($bill->water_consumption,3),'0'),'.')); ?> units)</span><span>$<?php echo e(number_format($bill->water_amount,2)); ?></span></div>
    <div class="kv-row"><span class="text-muted">Electricity (<?php echo e(rtrim(rtrim(number_format($bill->electric_consumption,3),'0'),'.')); ?> units)</span><span>$<?php echo e(number_format($bill->electric_amount,2)); ?></span></div>
    <div class="kv-row"><span class="text-muted">Parking</span><span>$<?php echo e(number_format($bill->parking_amount,2)); ?></span></div>
    <?php if($bill->late_fee > 0): ?><div class="kv-row"><span class="text-danger">Late Fee</span><span>$<?php echo e(number_format($bill->late_fee,2)); ?></span></div><?php endif; ?>
    <?php if($bill->discount_amount > 0): ?><div class="kv-row"><span class="text-success">Discount</span><span>−$<?php echo e(number_format($bill->discount_amount,2)); ?></span></div><?php endif; ?>
    <div class="kv-row kv-total"><span>Total</span><span>$<?php echo e(number_format($bill->total_amount,2)); ?></span></div>
    <div class="kv-row"><span class="text-muted">Paid</span><span>$<?php echo e(number_format($bill->amount_paid,2)); ?></span></div>
    <div class="kv-row fw-600"><span>Balance</span><span>$<?php echo e(number_format(max(0,$bill->total_amount-$bill->amount_paid),2)); ?></span></div>
  </div>
  <div class="card">
    <div class="card-title mb-4">Details</div>
    <div class="flex flex-col gap-2 text-md">
      <div><span class="text-muted">Tenant:</span> <?php echo e($bill->tenant?->full_name ?? '—'); ?> (<?php echo e($bill->tenant?->email ?? '—'); ?>)</div>
      <div><span class="text-muted">Unit:</span> <?php echo e($bill->unit?->unit_number ?? '—'); ?>, <?php echo e($bill->unit?->property?->name ?? '—'); ?></div>
      <div><span class="text-muted">Due Date:</span> <?php echo e($bill->due_date->format('M j, Y')); ?></div>
      <div><span class="text-muted">Status:</span>
        <?php $bs = $bill->status==='paid'?'success':($bill->status==='overdue'?'danger':'warning'); $bsi = $bill->status==='paid'?'check-circle':($bill->status==='overdue'?'alert':'clock'); ?>
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
<?php endif; ?><?php echo e(ucfirst(str_replace('_',' ',$bill->status))); ?></span>
      </div>
    </div>
  </div>
</div>

<div class="table-wrap table-stack">
  <div class="table-title">Payment History</div>
  <div class="table-scroll">
  <table>
    <thead><tr><th>Date</th><th>Amount</th><th>Method</th><th>Reference</th></tr></thead>
    <tbody>
      <?php $__empty_1 = true; $__currentLoopData = $bill->payments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
      <tr>
        <td data-label="Date"><?php echo e(\Illuminate\Support\Carbon::parse($p->payment_date)->format('M j, Y')); ?></td>
        <td data-label="Amount">$<?php echo e(number_format($p->amount,2)); ?></td>
        <td data-label="Method"><?php echo e(ucfirst(str_replace('_',' ',$p->payment_method))); ?></td>
        <td data-label="Reference" class="text-muted"><?php echo e($p->reference_number ?? '—'); ?></td>
      </tr>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
      <tr class="table-empty"><td colspan="4">
        <div class="empty-state">
          <div class="empty-icon"><?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
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
<?php endif; ?></div>
          <div class="empty-title">No payments recorded yet</div>
        </div>
      </td></tr>
      <?php endif; ?>
    </tbody>
  </table>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.owner', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\abdirahman\Downloads\SOM_Property_Web_Fixed (1)\som_web\resources\views/owner/billing/show.blade.php ENDPATH**/ ?>