<?php $__env->startSection('page-title','Reports'); ?>
<?php $__env->startSection('content'); ?>
<div class="stats-grid mb-5">
  <div class="stat-card"><div class="stat-label">Annual Revenue <?php echo e($year); ?></div><div class="stat-value">$<?php echo e(number_format($totals['annual_revenue'],0)); ?></div></div>
  <div class="stat-card"><div class="stat-label">Total Units</div><div class="stat-value"><?php echo e($totals['total_units']); ?></div><div class="stat-delta"><?php echo e($totals['occupancy_rate']); ?>% occupied</div></div>
  <div class="stat-card"><div class="stat-label">Occupied Units</div><div class="stat-value"><?php echo e($totals['occupied_units']); ?></div></div>
  <div class="stat-card"><div class="stat-label">Overdue Amount</div><div class="stat-value">$<?php echo e(number_format($totals['overdue_amount'],0)); ?></div><div class="stat-delta neg">outstanding</div></div>
</div>
<div class="card">
  <div class="card-title mb-4">Monthly Revenue Breakdown — <?php echo e($year); ?></div>
  <div class="table-wrap table-stack"><div class="table-scroll">
    <table>
      <thead><tr><th>Month</th><th>Rent Collected</th><th>Water</th><th>Electric</th><th>Total</th></tr></thead>
      <tbody>
        <?php $__currentLoopData = $monthlyRevenue; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <tr>
          <td data-label="Month" class="fw-500"><?php echo e($m['month']); ?></td>
          <td data-label="Rent Collected">$<?php echo e(number_format($m['rent'],2)); ?></td>
          <td data-label="Water" class="text-info">$<?php echo e(number_format($m['water'],2)); ?></td>
          <td data-label="Electric" class="text-gold">$<?php echo e(number_format($m['electric'],2)); ?></td>
          <td data-label="Total"><b>$<?php echo e(number_format($m['rent']+$m['water']+$m['electric'],2)); ?></b></td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </tbody>
    </table>
  </div></div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.owner', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\abdirahman\Downloads\SOM_Property_Web_Fixed (1)\som_web\resources\views/owner/reports/index.blade.php ENDPATH**/ ?>