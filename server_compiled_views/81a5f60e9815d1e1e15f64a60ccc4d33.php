<?php $__env->startSection('title', 'Apply for Leave'); ?>

<?php $__env->startPush('styles'); ?>
<style>.payroll-wrapper { max-width: 1200px; margin: 0 auto; padding: 0 20px; }</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid payroll-wrapper">
    <div class="smartdash-page-header" style="margin-bottom: 20px;">
        <div class="page-title">
            <div class="page-icon"><i class="fas fa-calendar-plus"></i></div>
            <div><h1>Apply for Leave</h1><p>Submit a new leave application</p></div>
        </div>
        <div class="page-breadcrumb">
            <a href="/cims/pm/system-settings"><i class="fas fa-home"></i> CIMS</a>
            <span class="separator">/</span>
            <a href="<?php echo e(route('cimspayroll.dashboard')); ?>">Payroll</a>
            <span class="separator">/</span>
            <a href="<?php echo e(route('cimspayroll.leave.applications')); ?>">Leave Applications</a>
            <span class="separator">/</span>
            <span class="current">Apply</span>
        </div>
        <a href="<?php echo e(route('cimspayroll.leave.applications')); ?>" class="btn button_master_close" style="color:#fff;"><i class="fa-solid fa-circle-left"></i> Close</a>
    </div>

    <?php if(session('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert"><i class="fa fa-exclamation-triangle me-2"></i><?php echo e(session('error')); ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    <?php endif; ?>

    <?php if($errors->any()): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fa fa-exclamation-triangle me-2"></i><strong>Please fix the following errors:</strong>
        <ul class="mb-0 mt-2"><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li><?php echo e($error); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <div class="row"><div class="col-12">
        <form method="POST" action="<?php echo e(route('cimspayroll.leave.applications.store')); ?>">
            <?php echo csrf_field(); ?>
            <div class="card smartdash-form-card">
                <div class="card-header"><h4><i class="fas fa-calendar-plus"></i> LEAVE APPLICATION</h4></div>
                <div class="card-body">

                    <div class="form-section-title"><i class="fa fa-user"></i> EMPLOYEE & LEAVE TYPE</div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Employee <span class="text-danger">*</span></label>
                                <select name="employee_id" class="form-control" required>
                                    <option value="">-- Select Employee --</option>
                                    <?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $emp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($emp->id); ?>" <?php echo e(old('employee_id') == $emp->id ? 'selected' : ''); ?>><?php echo e($emp->first_name); ?> <?php echo e($emp->last_name); ?> (#<?php echo e($emp->employee_number); ?>)</option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Leave Type <span class="text-danger">*</span></label>
                                <select name="leave_type_id" class="form-control" required>
                                    <option value="">-- Select Leave Type --</option>
                                    <?php $__currentLoopData = $leaveTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($lt->id); ?>" <?php echo e(old('leave_type_id') == $lt->id ? 'selected' : ''); ?>><?php echo e($lt->name); ?> (<?php echo e($lt->days_per_year); ?> days/yr<?php echo e($lt->is_paid ? '' : ' — Unpaid'); ?>)</option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-section-title"><i class="fa fa-calendar"></i> DATES</div>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Start Date <span class="text-danger">*</span></label>
                                <input type="date" name="start_date" id="start_date" class="form-control" value="<?php echo e(old('start_date')); ?>" required onchange="calcDays()">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">End Date <span class="text-danger">*</span></label>
                                <input type="date" name="end_date" id="end_date" class="form-control" value="<?php echo e(old('end_date')); ?>" required onchange="calcDays()">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Working Days Requested <span class="text-danger">*</span></label>
                                <input type="number" step="0.5" name="days_requested" id="days_requested" class="form-control" value="<?php echo e(old('days_requested')); ?>" required min="0.5">
                            </div>
                        </div>
                    </div>

                    <div class="form-section-title"><i class="fa fa-comment"></i> REASON</div>
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label class="form-label">Reason / Notes</label>
                                <textarea name="reason" class="form-control" rows="3" placeholder="Optional — reason for leave"><?php echo e(old('reason')); ?></textarea>
                            </div>
                        </div>
                    </div>

                    <div style="margin-top: 16px;">
                        <button type="submit" class="btn button_master_save"><i class="fa fa-paper-plane"></i> Submit Application</button>
                        <a href="<?php echo e(route('cimspayroll.leave.applications')); ?>" class="btn button_master_cancel"><i class="fa fa-times"></i> Cancel</a>
                    </div>
                </div>
            </div>
        </form>
    </div></div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
function calcDays() {
    var start = document.getElementById('start_date').value;
    var end = document.getElementById('end_date').value;
    if (start && end) {
        var s = new Date(start);
        var e = new Date(end);
        if (e >= s) {
            var days = 0;
            var d = new Date(s);
            while (d <= e) {
                var dow = d.getDay();
                if (dow !== 0 && dow !== 6) days++; // Exclude weekends
                d.setDate(d.getDate() + 1);
            }
            document.getElementById('days_requested').value = days;
        }
    }
}
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.default', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /usr/www/users/smartucbmh/application/Modules/CIMS_PAYROLL/Resources/views/payroll/leave/apply.blade.php ENDPATH**/ ?>