<?php $__env->startSection('title', 'Payroll Dashboard'); ?>

<?php $__env->startPush('styles'); ?>
<style>
.payroll-wrapper { max-width: 1200px; margin: 0 auto; padding: 0 20px; }
.stats-row { margin-bottom: 28px; }
.stat-card {
    border-radius: 12px; padding: 20px; color: #fff;
    position: relative; overflow: hidden;
    transition: all 0.3s ease; min-height: 120px;
}
.stat-card:hover { transform: translateY(-5px); box-shadow: 0 15px 35px rgba(0,0,0,0.2); }
.stat-card.sc-companies { background: linear-gradient(135deg, #0d3d56 0%, #17A2B8 100%); }
.stat-card.sc-employees { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); }
.stat-card.sc-income { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
.stat-card.sc-deductions { background: linear-gradient(135deg, #f2994a 0%, #f2c94c 100%); }
.stat-card .stat-label { font-size: 13px; font-weight: 500; opacity: 0.9; margin-bottom: 8px; }
.stat-card .stat-number { font-size: 36px; font-weight: 700; margin: 0; line-height: 1.1; }
.stat-card .stat-icon { position: absolute; right: 18px; bottom: 15px; font-size: 50px; opacity: 0.3; }

.quick-link-card {
    display: flex; align-items: center; gap: 14px;
    padding: 16px 20px; background: #fff; border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.06); text-decoration: none;
    color: #0d3d56; font-weight: 600; font-size: 15px;
    transition: all 0.2s; border-left: 4px solid #17A2B8;
}
.quick-link-card:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(23,162,184,0.2); color: #17A2B8; text-decoration: none; }
.quick-link-card i { font-size: 20px; color: #17A2B8; width: 24px; text-align: center; }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid payroll-wrapper">
    <div class="smartdash-page-header" style="margin-bottom: 20px;">
        <div class="page-title">
            <div class="page-icon"><i class="fas fa-money-check-alt"></i></div>
            <div>
                <h1>Payroll Management</h1>
                <p>South African BCEA Compliant Payroll System</p>
            </div>
        </div>
        <div class="page-breadcrumb">
            <a href="/cims/pm/system-settings"><i class="fas fa-home"></i> CIMS</a>
            <span class="separator">/</span>
            <span class="current">Payroll Dashboard</span>
        </div>
    </div>

    <!-- Stats -->
    <div class="row stats-row">
        <div class="col-md-3"><div class="stat-card sc-companies"><div class="stat-label">Active Companies</div><div class="stat-number"><?php echo e($stats['companies']); ?></div><div class="stat-icon"><i class="fas fa-building"></i></div></div></div>
        <div class="col-md-3"><div class="stat-card sc-employees"><div class="stat-label">Active Employees</div><div class="stat-number"><?php echo e($stats['employees']); ?></div><div class="stat-icon"><i class="fas fa-users"></i></div></div></div>
        <div class="col-md-3"><div class="stat-card sc-income"><div class="stat-label">Income Types</div><div class="stat-number"><?php echo e($stats['income_types']); ?></div><div class="stat-icon"><i class="fas fa-plus-circle"></i></div></div></div>
        <div class="col-md-3"><div class="stat-card sc-deductions"><div class="stat-label">Deduction Types</div><div class="stat-number"><?php echo e($stats['deduction_types']); ?></div><div class="stat-icon"><i class="fas fa-minus-circle"></i></div></div></div>
    </div>

    <!-- Quick Links -->
    <div class="row">
        <div class="col-12">
            <div class="card smartdash-form-card">
                <div class="card-header"><h4><i class="fas fa-cogs"></i> SETUP</h4></div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3"><a href="<?php echo e(route('cimspayroll.companies.index')); ?>" class="quick-link-card"><i class="fas fa-building"></i> Companies</a></div>
                        <div class="col-md-4 mb-3"><a href="<?php echo e(route('cimspayroll.employees.index')); ?>" class="quick-link-card"><i class="fas fa-users"></i> Employees</a></div>
                        <div class="col-md-4 mb-3"><a href="<?php echo e(route('cimspayroll.income-types.index')); ?>" class="quick-link-card"><i class="fas fa-plus-circle"></i> Income Types</a></div>
                        <div class="col-md-4 mb-3"><a href="<?php echo e(route('cimspayroll.deduction-types.index')); ?>" class="quick-link-card"><i class="fas fa-minus-circle"></i> Deduction Types</a></div>
                        <div class="col-md-4 mb-3"><a href="<?php echo e(route('cimspayroll.contribution-types.index')); ?>" class="quick-link-card"><i class="fas fa-handshake"></i> Company Contributions</a></div>
                        <div class="col-md-4 mb-3"><a href="<?php echo e(route('cimspayroll.tax-tables.index')); ?>" class="quick-link-card"><i class="fas fa-calculator"></i> Tax Tables</a></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Employees -->
    <div class="row">
        <div class="col-12">
            <div class="card smartdash-form-card">
                <div class="card-header"><h4><i class="fas fa-user-clock"></i> RECENTLY ADDED EMPLOYEES</h4></div>
                <div class="card-body" style="padding: 0;">
                    <?php if($recentEmployees->count() > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead style="background:#f8f9fa;"><tr><th>Employee #</th><th>Name</th><th>Company</th><th>Job Title</th><th>Status</th><th>Added</th></tr></thead>
                            <tbody>
                                <?php $__currentLoopData = $recentEmployees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $emp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><?php echo e($emp->employee_number); ?></td>
                                    <td><strong><?php echo e($emp->first_name); ?> <?php echo e($emp->last_name); ?></strong></td>
                                    <td><?php echo e($emp->company->company_name ?? '—'); ?></td>
                                    <td><?php echo e($emp->job_title ?? '—'); ?></td>
                                    <td><span class="badge bg-<?php echo e($emp->status === 'active' ? 'success' : ($emp->status === 'terminated' ? 'danger' : 'warning')); ?>"><?php echo e(ucfirst($emp->status)); ?></span></td>
                                    <td><?php echo e($emp->created_at->format('d M Y')); ?></td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                    <?php else: ?>
                    <div style="padding: 40px; text-align: center; color: #999;">
                        <i class="fas fa-user-plus" style="font-size: 40px; margin-bottom: 12px; display: block;"></i>
                        <p>No employees added yet. Start by adding a <a href="<?php echo e(route('cimspayroll.companies.index')); ?>">company</a>, then add employees.</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.default', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /usr/www/users/smartucbmh/application/Modules/CIMS_PAYROLL/Resources/views/payroll/dashboard.blade.php ENDPATH**/ ?>