<?php $__env->startSection('page-title','System Settings'); ?>
<?php $__env->startSection('content'); ?>
<div class="grid-2">
  <div class="card">
    <div class="card-title mb-4">Platform Settings</div>
    <form method="POST" action="<?php echo e(route('admin.settings.update')); ?>"><?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
      <div class="form-group"><label class="form-label">Platform Name</label><input class="form-control" name="app_name" value="SOM Property Management"></div>
      <div class="form-group"><label class="form-label">Support Email</label><input class="form-control" name="support_email" value="support@somproperty.com"></div>
      <div class="form-group"><label class="form-label">Default Currency</label>
        <select class="form-control" name="currency"><option>USD ($)</option><option>EUR (€)</option><option>GBP (£)</option></select>
      </div>
      <div class="form-group"><label class="form-label">Trial Days</label><input class="form-control" type="number" name="trial_days" value="14"></div>
      <button class="btn btn-primary" type="submit">Save Settings</button>
    </form>
  </div>
  <div class="card">
    <div class="card-title mb-4">System Info</div>
    <div class="flex flex-col text-md">
      <div class="kv-row"><span class="text-muted">PHP Version</span><b><?php echo e(PHP_VERSION); ?></b></div>
      <div class="kv-row"><span class="text-muted">Laravel Version</span><b><?php echo e(app()->version()); ?></b></div>
      <div class="kv-row"><span class="text-muted">Database</span><b>MySQL 8.0</b></div>
      <div class="kv-row"><span class="text-muted">Environment</span><b><?php echo e(config('app.env')); ?></b></div>
      <div class="kv-row"><span class="text-muted">Server Time</span><b><?php echo e(now()->format('M j, Y H:i T')); ?></b></div>
    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\abdirahman\Downloads\SOM_Property_Web_Fixed (1)\som_web\resources\views/admin/settings.blade.php ENDPATH**/ ?>