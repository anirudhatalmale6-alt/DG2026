<?php $__env->startSection('title', 'Leave Applications'); ?>

<?php $__env->startPush('styles'); ?>
<style>.payroll-wrapper { max-width: 1200px; margin: 0 auto; padding: 0 20px; }</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid payroll-wrapper">
    <div class="smartdash-page-header" style="margin-bottom: 20px;">
        <div class="page-title">
            <div class="page-icon"><i class="fas fa-file-alt"></i></div>
            <div><h1>Leave Applications</h1><p>Manage employee leave requests</p></div>
        </div>
        <div class="page-breadcrumb">
            <a href="/cims/pm/system-settings"><i class="fas fa-home"></i> CIMS</a>
            <span class="separator">/</span>
            <a href="<?php echo e(route('cimspayroll.dashboard')); ?>">Payroll</a>
            <span class="separator">/</span>
            <span class="current">Leave Applications</span>
        </div>
        <div style="display:flex;gap:8px;">
            <a href="<?php echo e(route('cimspayroll.leave.applications.create')); ?>" class="btn button_master_add"><i class="fa fa-plus"></i> New Application</a>
            <a href="<?php echo e(route('cimspayroll.dashboard')); ?>" class="btn button_master_close" style="color:#fff;"><i class="fa-solid fa-circle-left"></i> Close</a>
        </div>
    </div>

    <?php if(session('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert"><i class="fa fa-check-circle me-2"></i><?php echo e(session('success')); ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    <?php endif; ?>
    <?php if(session('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert"><i class="fa fa-exclamation-triangle me-2"></i><?php echo e(session('error')); ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    <?php endif; ?>

    <!-- Filters -->
    <div class="row"><div class="col-12">
        <div class="card smartdash-form-card">
            <div class="card-header"><h4><i class="fas fa-filter"></i> FILTER</h4></div>
            <div class="card-body">
                <form method="GET" action="<?php echo e(route('cimspayroll.leave.applications')); ?>">
                    <div class="row">
                        <div class="col-md-3">
                            <select name="company_id" class="form-control">
                                <option value="">-- All Companies --</option>
                                <?php $__currentLoopData = $companies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($c->id); ?>" <?php echo e(request('company_id') == $c->id ? 'selected' : ''); ?>><?php echo e($c->company_name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select name="status" class="form-control">
                                <option value="">-- All Status --</option>
                                <option value="pending" <?php echo e(request('status') === 'pending' ? 'selected' : ''); ?>>Pending</option>
                                <option value="approved" <?php echo e(request('status') === 'approved' ? 'selected' : ''); ?>>Approved</option>
                                <option value="rejected" <?php echo e(request('status') === 'rejected' ? 'selected' : ''); ?>>Rejected</option>
                                <option value="cancelled" <?php echo e(request('status') === 'cancelled' ? 'selected' : ''); ?>>Cancelled</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn button_master_search"><i class="fa fa-search"></i> Filter</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div></div>

    <!-- Applications List -->
    <div class="row"><div class="col-12">
        <div class="card smartdash-form-card">
            <div class="card-header"><h4><i class="fas fa-list"></i> LEAVE APPLICATIONS</h4></div>
            <div class="card-body" style="padding:0;">
                <?php if($applications->count() > 0): ?>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead style="background:#f8f9fa;">
                            <tr><th>Employee</th><th>Leave Type</th><th>From</th><th>To</th><th>Days</th><th>Reason</th><th>Status</th><th>Actions</th></tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $applications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $app): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><strong><?php echo e($app->employee->first_name ?? ''); ?> <?php echo e($app->employee->last_name ?? ''); ?></strong><br><small class="text-muted">#<?php echo e($app->employee->employee_number ?? ''); ?></small></td>
                                <td><?php echo e($app->leaveType->name ?? '—'); ?></td>
                                <td><?php echo e($app->start_date->format('d M Y')); ?></td>
                                <td><?php echo e($app->end_date->format('d M Y')); ?></td>
                                <td><strong><?php echo e($app->days_requested); ?></strong></td>
                                <td style="max-width:200px;"><?php echo e(\Illuminate\Support\Str::limit($app->reason, 60)); ?></td>
                                <td>
                                    <?php
                                        $statusColors = ['pending' => 'warning', 'approved' => 'success', 'rejected' => 'danger', 'cancelled' => 'secondary'];
                                    ?>
                                    <span class="badge bg-<?php echo e($statusColors[$app->status] ?? 'secondary'); ?>"><?php echo e(ucfirst($app->status)); ?></span>
                                    <?php if($app->status === 'rejected' && $app->rejection_reason): ?>
                                    <br><small class="text-danger"><?php echo e($app->rejection_reason); ?></small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if($app->status === 'pending'): ?>
                                    <form method="POST" action="<?php echo e(route('cimspayroll.leave.applications.approve', $app->id)); ?>" style="display:inline;" onsubmit="return confirm('Approve this leave?');">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit" class="btn button_master_save" style="padding:4px 12px;font-size:12px;"><i class="fa fa-check"></i> Approve</button>
                                    </form>
                                    <button type="button" class="btn button_master_delete" style="padding:4px 12px;font-size:12px;" onclick="rejectLeave(<?php echo e($app->id); ?>)"><i class="fa fa-times"></i> Reject</button>
                                    <?php endif; ?>
                                    <?php if(in_array($app->status, ['pending', 'approved'])): ?>
                                    <form method="POST" action="<?php echo e(route('cimspayroll.leave.applications.cancel', $app->id)); ?>" style="display:inline;" onsubmit="return confirm('Cancel this leave application?');">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit" class="btn button_master_cancel" style="padding:4px 12px;font-size:12px;"><i class="fa fa-ban"></i></button>
                                    </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
                <div class="p-3"><?php echo e($applications->withQueryString()->links()); ?></div>
                <?php else: ?>
                <div style="text-align:center;padding:40px;color:#999;">
                    <i class="fas fa-file-alt" style="font-size:48px;margin-bottom:12px;display:block;"></i>
                    <p>No leave applications found.</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div></div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" id="rejectForm">
                <?php echo csrf_field(); ?>
                <div class="modal-header"><h5 class="modal-title">Reject Leave</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Reason for Rejection</label>
                        <textarea name="rejection_reason" class="form-control" rows="3" placeholder="Optional — reason for rejecting this leave"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn button_master_cancel" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn button_master_delete"><i class="fa fa-times"></i> Reject</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
function rejectLeave(id) {
    document.getElementById('rejectForm').action = '/cims/payroll/leave/applications/' + id + '/reject';
    new bootstrap.Modal(document.getElementById('rejectModal')).show();
}
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.default', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /usr/www/users/smartucbmh/application/Modules/CIMS_PAYROLL/Resources/views/payroll/leave/applications.blade.php ENDPATH**/ ?>