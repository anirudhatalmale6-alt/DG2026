<?php $__env->startSection('title', 'Leave Balances'); ?>

<?php $__env->startPush('styles'); ?>
<style>
.payroll-wrapper { max-width: 1200px; margin: 0 auto; padding: 0 20px; }
.edit-row { display: none; }
.edit-row.active { display: table-row; }
.view-row.hidden { display: none; }
.edit-row input { height: 34px; border: 2px solid #17A2B8; border-radius: 6px; padding: 0 8px; font-size: 13px; }
.bal-good { color: #059669; font-weight: 600; }
.bal-warn { color: #d97706; font-weight: 600; }
.bal-bad { color: #dc2626; font-weight: 600; }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid payroll-wrapper">
    <div class="smartdash-page-header" style="margin-bottom: 20px;">
        <div class="page-title">
            <div class="page-icon"><i class="fas fa-balance-scale"></i></div>
            <div><h1>Leave Balances</h1><p>View and manage employee leave balances for <?php echo e($year); ?></p></div>
        </div>
        <div class="page-breadcrumb">
            <a href="/cims/pm/system-settings"><i class="fas fa-home"></i> CIMS</a>
            <span class="separator">/</span>
            <a href="<?php echo e(route('cimspayroll.dashboard')); ?>">Payroll</a>
            <span class="separator">/</span>
            <span class="current">Leave Balances</span>
        </div>
        <a href="<?php echo e(route('cimspayroll.dashboard')); ?>" class="btn button_master_close" style="color:#fff;"><i class="fa-solid fa-circle-left"></i> Close</a>
    </div>

    <?php if(session('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert"><i class="fa fa-check-circle me-2"></i><?php echo e(session('success')); ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    <?php endif; ?>

    <!-- Filters -->
    <div class="row"><div class="col-12">
        <div class="card smartdash-form-card">
            <div class="card-header"><h4><i class="fas fa-filter"></i> FILTER & INITIALIZE</h4></div>
            <div class="card-body">
                <form method="GET" action="<?php echo e(route('cimspayroll.leave.balances')); ?>">
                    <div class="row">
                        <div class="col-md-2">
                            <div class="mb-3">
                                <label class="form-label">Year</label>
                                <select name="year" class="form-control">
                                    <?php for($y = date('Y') + 1; $y >= date('Y') - 2; $y--): ?>
                                    <option value="<?php echo e($y); ?>" <?php echo e($year == $y ? 'selected' : ''); ?>><?php echo e($y); ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Company</label>
                                <select name="company_id" class="form-control">
                                    <option value="">-- All Companies --</option>
                                    <?php $__currentLoopData = $companies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($c->id); ?>" <?php echo e($companyId == $c->id ? 'selected' : ''); ?>><?php echo e($c->company_name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="mb-3">
                                <label class="form-label">&nbsp;</label><br>
                                <button type="submit" class="btn button_master_search"><i class="fa fa-search"></i> Filter</button>
                            </div>
                        </div>
                    </div>
                </form>
                <hr>
                <form method="POST" action="<?php echo e(route('cimspayroll.leave.balances.init')); ?>" onsubmit="return confirm('This will create leave balance records for all active employees. Continue?');">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="year" value="<?php echo e($year); ?>">
                    <input type="hidden" name="company_id" value="<?php echo e($companyId); ?>">
                    <button type="submit" class="btn button_master_add"><i class="fa fa-magic"></i> Initialize Balances for <?php echo e($year); ?></button>
                    <small class="text-muted ms-2">Creates leave balance records for all active employees who don't have one yet.</small>
                </form>
            </div>
        </div>
    </div></div>

    <!-- Balances Table -->
    <div class="row"><div class="col-12">
        <div class="card smartdash-form-card">
            <div class="card-header"><h4><i class="fas fa-list"></i> LEAVE BALANCES — <?php echo e($year); ?> (<?php echo e($employees->count()); ?> employees)</h4></div>
            <div class="card-body" style="padding:0;">
                <?php if($employees->count() > 0 && $leaveTypes->count() > 0): ?>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead style="background:#f8f9fa;">
                            <tr>
                                <th>Employee</th>
                                <th>Company</th>
                                <?php $__currentLoopData = $leaveTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <th style="text-align:center;font-size:11px;"><?php echo e($lt->code); ?><br><small><?php echo e($lt->days_per_year); ?>d</small></th>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $emp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php $empBalances = $balances->get($emp->id, collect()); ?>
                            <tr>
                                <td><strong><?php echo e($emp->first_name); ?> <?php echo e($emp->last_name); ?></strong><br><small class="text-muted">#<?php echo e($emp->employee_number); ?></small></td>
                                <td><?php echo e($emp->company->company_name ?? '—'); ?></td>
                                <?php $__currentLoopData = $leaveTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $bal = $empBalances->firstWhere('leave_type_id', $lt->id);
                                    $remaining = $bal ? ($bal->entitled_days + $bal->carried_forward - $bal->taken_days - $bal->pending_days) : null;
                                    $cls = $remaining === null ? '' : ($remaining > 5 ? 'bal-good' : ($remaining > 0 ? 'bal-warn' : 'bal-bad'));
                                ?>
                                <td style="text-align:center;">
                                    <?php if($bal): ?>
                                    <span class="<?php echo e($cls); ?>"><?php echo e(number_format($remaining, 1)); ?></span>
                                    <br><small class="text-muted">T:<?php echo e($bal->taken_days); ?> P:<?php echo e($bal->pending_days); ?></small>
                                    <?php else: ?>
                                    <span class="text-muted">—</span>
                                    <?php endif; ?>
                                </td>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div style="text-align:center;padding:40px;color:#999;">
                    <i class="fas fa-calendar-times" style="font-size:48px;margin-bottom:12px;display:block;"></i>
                    <p>No employees or leave types found. Set up employees and leave types first, then initialize balances.</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div></div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.default', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /usr/www/users/smartucbmh/application/Modules/CIMS_PAYROLL/Resources/views/payroll/leave/balances.blade.php ENDPATH**/ ?>