<?php $__env->startSection('title', ($timesheet ? 'Edit' : 'New') . ' Timesheet'); ?>

<?php $__env->startPush('styles'); ?>
<style>.payroll-wrapper { max-width: 1200px; margin: 0 auto; padding: 0 20px; }</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid payroll-wrapper">
    <div class="smartdash-page-header" style="margin-bottom: 20px;">
        <div class="page-title">
            <div class="page-icon"><i class="fas fa-clock"></i></div>
            <div>
                <h1><?php echo e($timesheet ? 'Edit' : 'New'); ?> Timesheet</h1>
                <p><?php echo e($timesheet ? 'Update timesheet hours' : 'Record hours for an employee'); ?></p>
            </div>
        </div>
        <div class="page-breadcrumb">
            <a href="/cims/pm/system-settings"><i class="fas fa-home"></i> CIMS</a>
            <span class="separator">/</span>
            <a href="<?php echo e(route('cimspayroll.dashboard')); ?>">Payroll</a>
            <span class="separator">/</span>
            <a href="<?php echo e(route('cimspayroll.timesheets.index')); ?>">Timesheets</a>
            <span class="separator">/</span>
            <span class="current"><?php echo e($timesheet ? 'Edit' : 'New'); ?></span>
        </div>
        <a href="<?php echo e(route('cimspayroll.timesheets.index')); ?>" class="btn button_master_close" style="color:#fff;"><i class="fa-solid fa-circle-left"></i> Close</a>
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
        <form method="POST" action="<?php echo e($timesheet ? route('cimspayroll.timesheets.update', $timesheet->id) : route('cimspayroll.timesheets.store')); ?>">
            <?php echo csrf_field(); ?>
            <?php if($timesheet): ?> <?php echo method_field('PUT'); ?> <?php endif; ?>

            <div class="card smartdash-form-card">
                <div class="card-header"><h4><i class="fas fa-clock"></i> TIMESHEET ENTRY</h4></div>
                <div class="card-body">

                    <div class="form-section-title"><i class="fa fa-user"></i> EMPLOYEE & PERIOD</div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Employee <span class="text-danger">*</span></label>
                                <select name="employee_id" class="form-control" required>
                                    <option value="">-- Select Employee --</option>
                                    <?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $emp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($emp->id); ?>" <?php echo e(old('employee_id', $timesheet->employee_id ?? '') == $emp->id ? 'selected' : ''); ?>><?php echo e($emp->first_name); ?> <?php echo e($emp->last_name); ?> (#<?php echo e($emp->employee_number); ?>) — <?php echo e($emp->company->company_name ?? ''); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Period Start <span class="text-danger">*</span></label>
                                <input type="date" name="period_start" class="form-control" value="<?php echo e(old('period_start', $timesheet ? $timesheet->period_start->format('Y-m-d') : date('Y-m-01'))); ?>" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Period End <span class="text-danger">*</span></label>
                                <input type="date" name="period_end" class="form-control" value="<?php echo e(old('period_end', $timesheet ? $timesheet->period_end->format('Y-m-d') : date('Y-m-t'))); ?>" required>
                            </div>
                        </div>
                    </div>

                    <div class="form-section-title"><i class="fa fa-clock"></i> HOURS WORKED</div>
                    <div class="row">
                        <div class="col-md-2">
                            <div class="mb-3">
                                <label class="form-label">Normal Hours <span class="text-danger">*</span></label>
                                <input type="number" step="0.5" name="normal_hours" class="form-control" value="<?php echo e(old('normal_hours', $timesheet->normal_hours ?? '195.00')); ?>" required min="0">
                                <small class="text-muted">BCEA: 195 hrs/month</small>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="mb-3">
                                <label class="form-label">OT 1.5x Hours</label>
                                <input type="number" step="0.5" name="overtime_15x_hours" class="form-control" value="<?php echo e(old('overtime_15x_hours', $timesheet->overtime_15x_hours ?? '0')); ?>" min="0">
                                <small class="text-muted">Weekday overtime</small>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="mb-3">
                                <label class="form-label">OT 2x Hours</label>
                                <input type="number" step="0.5" name="overtime_2x_hours" class="form-control" value="<?php echo e(old('overtime_2x_hours', $timesheet->overtime_2x_hours ?? '0')); ?>" min="0">
                                <small class="text-muted">Double time</small>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="mb-3">
                                <label class="form-label">Sunday Hours</label>
                                <input type="number" step="0.5" name="sunday_hours" class="form-control" value="<?php echo e(old('sunday_hours', $timesheet->sunday_hours ?? '0')); ?>" min="0">
                                <small class="text-muted">Sunday work</small>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="mb-3">
                                <label class="form-label">Public Holiday</label>
                                <input type="number" step="0.5" name="public_holiday_hours" class="form-control" value="<?php echo e(old('public_holiday_hours', $timesheet->public_holiday_hours ?? '0')); ?>" min="0">
                                <small class="text-muted">PH hours</small>
                            </div>
                        </div>
                    </div>

                    <div class="form-section-title"><i class="fa fa-calendar-alt"></i> DAYS</div>
                    <div class="row">
                        <div class="col-md-2">
                            <div class="mb-3">
                                <label class="form-label">Days Worked <span class="text-danger">*</span></label>
                                <input type="number" step="0.5" name="days_worked" class="form-control" value="<?php echo e(old('days_worked', $timesheet->days_worked ?? '21.67')); ?>" required min="0">
                                <small class="text-muted">BCEA: 21.67/month</small>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="mb-3">
                                <label class="form-label">Days Absent</label>
                                <input type="number" step="0.5" name="days_absent" class="form-control" value="<?php echo e(old('days_absent', $timesheet->days_absent ?? '0')); ?>" min="0">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="mb-3">
                                <label class="form-label">Days Leave</label>
                                <input type="number" step="0.5" name="days_leave" class="form-control" value="<?php echo e(old('days_leave', $timesheet->days_leave ?? '0')); ?>" min="0">
                            </div>
                        </div>
                    </div>

                    <div class="form-section-title"><i class="fa fa-sticky-note"></i> NOTES</div>
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <textarea name="notes" class="form-control" rows="2" placeholder="Optional notes"><?php echo e(old('notes', $timesheet->notes ?? '')); ?></textarea>
                            </div>
                        </div>
                    </div>

                    <div style="margin-top: 16px;">
                        <button type="submit" class="btn <?php echo e($timesheet ? 'button_master_update' : 'button_master_save'); ?>"><i class="fa fa-save"></i> <?php echo e($timesheet ? 'Update Timesheet' : 'Save Timesheet'); ?></button>
                        <a href="<?php echo e(route('cimspayroll.timesheets.index')); ?>" class="btn button_master_cancel"><i class="fa fa-times"></i> Cancel</a>
                    </div>
                </div>
            </div>
        </form>
    </div></div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.default', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /usr/www/users/smartucbmh/application/Modules/CIMS_PAYROLL/Resources/views/payroll/timesheets/form.blade.php ENDPATH**/ ?>