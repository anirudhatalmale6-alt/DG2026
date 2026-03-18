<?php $__env->startSection('title', 'Leave Types'); ?>

<?php $__env->startPush('styles'); ?>
<style>
.payroll-wrapper { max-width: 1200px; margin: 0 auto; padding: 0 20px; }
.edit-row { display: none; }
.edit-row.active { display: table-row; }
.view-row.hidden { display: none; }
.edit-row input, .edit-row select { height: 34px; border: 2px solid #17A2B8; border-radius: 6px; padding: 0 8px; font-size: 13px; }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid payroll-wrapper">
    <div class="smartdash-page-header" style="margin-bottom: 20px;">
        <div class="page-title">
            <div class="page-icon"><i class="fas fa-calendar-check"></i></div>
            <div><h1>Leave Types</h1><p>Manage leave types (BCEA compliant)</p></div>
        </div>
        <div class="page-breadcrumb">
            <a href="/cims/pm/system-settings"><i class="fas fa-home"></i> CIMS</a>
            <span class="separator">/</span>
            <a href="<?php echo e(route('cimspayroll.dashboard')); ?>">Payroll</a>
            <span class="separator">/</span>
            <span class="current">Leave Types</span>
        </div>
        <a href="<?php echo e(route('cimspayroll.dashboard')); ?>" class="btn button_master_close" style="color:#fff;"><i class="fa-solid fa-circle-left"></i> Close</a>
    </div>

    <?php if(session('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert"><i class="fa fa-check-circle me-2"></i><?php echo e(session('success')); ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    <?php endif; ?>
    <?php if(session('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert"><i class="fa fa-exclamation-triangle me-2"></i><?php echo e(session('error')); ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    <?php endif; ?>

    <!-- Add New -->
    <div class="row"><div class="col-12">
        <div class="card smartdash-form-card">
            <div class="card-header"><h4><i class="fas fa-plus"></i> ADD NEW LEAVE TYPE</h4></div>
            <div class="card-body">
                <form method="POST" action="<?php echo e(route('cimspayroll.leave-types.store')); ?>">
                    <?php echo csrf_field(); ?>
                    <div class="row">
                        <div class="col-md-3"><div class="mb-3"><label class="form-label">Name <span class="text-danger">*</span></label><input type="text" name="name" class="form-control" required placeholder="e.g. Study Leave"></div></div>
                        <div class="col-md-2"><div class="mb-3"><label class="form-label">Code <span class="text-danger">*</span></label><input type="text" name="code" class="form-control" required placeholder="e.g. STUDY" maxlength="20"></div></div>
                        <div class="col-md-1"><div class="mb-3"><label class="form-label">Days/Yr</label><input type="number" step="0.5" name="days_per_year" class="form-control" value="0" min="0"></div></div>
                        <div class="col-md-1"><div class="mb-3"><label class="form-label">Cycle</label><input type="number" name="cycle_years" class="form-control" value="1" min="1" max="10"></div></div>
                        <div class="col-md-1"><div class="mb-3"><label class="form-label">Paid</label><select name="is_paid" class="form-control"><option value="1">Yes</option><option value="0">No</option></select></div></div>
                        <div class="col-md-1"><div class="mb-3"><label class="form-label">Stat.</label><select name="is_statutory" class="form-control"><option value="0">No</option><option value="1">Yes</option></select></div></div>
                        <div class="col-md-1"><div class="mb-3"><label class="form-label">Order</label><input type="number" name="sort_order" class="form-control" value="0" min="0"></div></div>
                        <div class="col-md-2"><div class="mb-3"><label class="form-label">Description</label><input type="text" name="description" class="form-control" placeholder="Optional"></div></div>
                    </div>
                    <button type="submit" class="btn button_master_save" style="padding:6px 16px;font-size:13px;"><i class="fa fa-plus"></i> Add</button>
                </form>
            </div>
        </div>
    </div></div>

    <!-- List -->
    <div class="row"><div class="col-12">
        <div class="card smartdash-form-card">
            <div class="card-header"><h4><i class="fas fa-list"></i> LEAVE TYPES (<?php echo e($types->count()); ?>)</h4></div>
            <div class="card-body" style="padding:0;">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead style="background:#f8f9fa;"><tr><th>#</th><th>Name</th><th>Code</th><th>Days/Year</th><th>Cycle</th><th>Paid</th><th>Statutory</th><th>Status</th><th>Actions</th></tr></thead>
                        <tbody>
                            <?php $__currentLoopData = $types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr class="view-row" id="view-<?php echo e($type->id); ?>">
                                <td><?php echo e($i + 1); ?></td>
                                <td><strong><?php echo e($type->name); ?></strong><?php if($type->description): ?><br><small class="text-muted"><?php echo e($type->description); ?></small><?php endif; ?></td>
                                <td><code><?php echo e($type->code); ?></code></td>
                                <td><?php echo e($type->days_per_year); ?></td>
                                <td><?php echo e($type->cycle_years == 1 ? 'Annual' : $type->cycle_years . '-year'); ?></td>
                                <td><span class="badge bg-<?php echo e($type->is_paid ? 'success' : 'secondary'); ?>"><?php echo e($type->is_paid ? 'Paid' : 'Unpaid'); ?></span></td>
                                <td><span class="badge bg-<?php echo e($type->is_statutory ? 'info' : 'secondary'); ?>"><?php echo e($type->is_statutory ? 'BCEA' : 'Custom'); ?></span></td>
                                <td><span class="badge bg-<?php echo e($type->is_active ? 'success' : 'secondary'); ?>"><?php echo e($type->is_active ? 'Active' : 'Inactive'); ?></span></td>
                                <td>
                                    <button type="button" class="btn button_master_edit" style="padding:4px 12px;font-size:12px;" onclick="toggleEdit(<?php echo e($type->id); ?>)"><i class="fa fa-edit"></i></button>
                                    <?php if(!$type->is_statutory): ?>
                                    <form method="POST" action="<?php echo e(route('cimspayroll.leave-types.destroy', $type->id)); ?>" onsubmit="return confirm('Delete?');" style="display:inline;">
                                        <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="btn button_master_delete" style="padding:4px 12px;font-size:12px;"><i class="fa fa-trash"></i></button>
                                    </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr class="edit-row" id="edit-<?php echo e($type->id); ?>">
                                <td><?php echo e($i + 1); ?></td>
                                <td colspan="8">
                                    <form method="POST" action="<?php echo e(route('cimspayroll.leave-types.update', $type->id)); ?>" style="display:flex;gap:8px;flex-wrap:wrap;align-items:center;">
                                        <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                                        <input type="text" name="name" value="<?php echo e($type->name); ?>" required style="flex:2;min-width:120px;">
                                        <input type="text" name="code" value="<?php echo e($type->code); ?>" required style="width:80px;">
                                        <input type="number" step="0.5" name="days_per_year" value="<?php echo e($type->days_per_year); ?>" style="width:70px;" min="0">
                                        <input type="number" name="cycle_years" value="<?php echo e($type->cycle_years); ?>" style="width:55px;" min="1">
                                        <select name="is_paid" style="width:80px;"><option value="1" <?php echo e($type->is_paid ? 'selected' : ''); ?>>Paid</option><option value="0" <?php echo e(!$type->is_paid ? 'selected' : ''); ?>>Unpaid</option></select>
                                        <select name="is_statutory" style="width:80px;"><option value="0" <?php echo e(!$type->is_statutory ? 'selected' : ''); ?>>Custom</option><option value="1" <?php echo e($type->is_statutory ? 'selected' : ''); ?>>BCEA</option></select>
                                        <select name="is_active" style="width:90px;"><option value="1" <?php echo e($type->is_active ? 'selected' : ''); ?>>Active</option><option value="0" <?php echo e(!$type->is_active ? 'selected' : ''); ?>>Inactive</option></select>
                                        <input type="number" name="sort_order" value="<?php echo e($type->sort_order); ?>" min="0" style="width:55px;">
                                        <input type="text" name="description" value="<?php echo e($type->description); ?>" placeholder="Description" style="flex:2;min-width:100px;">
                                        <button type="submit" class="btn button_master_save" style="padding:4px 14px;font-size:12px;"><i class="fa fa-check"></i></button>
                                        <button type="button" class="btn button_master_cancel" style="padding:4px 14px;font-size:12px;" onclick="toggleEdit(<?php echo e($type->id); ?>)"><i class="fa fa-times"></i></button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div></div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
function toggleEdit(id) {
    document.getElementById('view-' + id).classList.toggle('hidden');
    document.getElementById('edit-' + id).classList.toggle('active');
}
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.default', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /usr/www/users/smartucbmh/application/Modules/CIMS_PAYROLL/Resources/views/payroll/leave-types/index.blade.php ENDPATH**/ ?>