<?php $__env->startSection('title', 'Payslips — ' . $payRun->pay_period); ?>

<?php $__env->startPush('styles'); ?>
<style>
.payroll-wrapper { max-width: 1200px; margin: 0 auto; padding: 0 20px; }
.payslip-card { border: 1px solid #e0e0e0; border-radius: 8px; padding: 16px; margin-bottom: 12px; transition: background 0.15s; }
.payslip-card:hover { background: #f8f9fa; }
.payslip-emp-name { font-size: 15px; font-weight: 700; color: #004D40; }
.payslip-detail { font-size: 12px; color: #666; }
.payslip-amount { font-size: 18px; font-weight: 800; color: #155724; text-align: right; }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid payroll-wrapper">
    <div class="smartdash-page-header" style="margin-bottom: 20px;">
        <div class="page-title">
            <div class="page-icon"><i class="fas fa-file-invoice-dollar"></i></div>
            <div>
                <h1>Payslips: <?php echo e($payRun->pay_period); ?></h1>
                <p><?php echo e($payRun->company->company_name ?? ''); ?> — <?php echo e($payRun->period_start->format('d M')); ?> to <?php echo e($payRun->period_end->format('d M Y')); ?></p>
            </div>
        </div>
        <div class="page-breadcrumb">
            <a href="/cims/pm/system-settings"><i class="fas fa-home"></i> CIMS</a>
            <span class="separator">/</span>
            <a href="<?php echo e(route('cimspayroll.dashboard')); ?>">Payroll</a>
            <span class="separator">/</span>
            <a href="<?php echo e(route('cimspayroll.payslips.index')); ?>">Payslips</a>
            <span class="separator">/</span>
            <span class="current"><?php echo e($payRun->pay_period); ?></span>
        </div>
        <div style="display:flex;gap:8px;">
            <a href="<?php echo e(route('cimspayroll.payslips.download-bulk', $payRun->id)); ?>" class="btn button_master_save"><i class="fa fa-download"></i> Download All (<?php echo e($payRun->lines->count()); ?> Payslips)</a>
            <a href="<?php echo e(route('cimspayroll.payslips.index')); ?>" class="btn button_master_close" style="color:#fff;"><i class="fa-solid fa-circle-left"></i> Close</a>
        </div>
    </div>

    <!-- Summary -->
    <div class="row" style="margin-bottom: 16px;">
        <div class="col-md-3">
            <div style="background:#E0F2F1;border-left:4px solid #009688;padding:12px 16px;border-radius:4px;">
                <small style="color:#00796B;">Employees</small>
                <h4 style="margin:4px 0 0;color:#004D40;"><?php echo e($payRun->lines->count()); ?></h4>
            </div>
        </div>
        <div class="col-md-3">
            <div style="background:#E0F2F1;border-left:4px solid #009688;padding:12px 16px;border-radius:4px;">
                <small style="color:#00796B;">Total Gross</small>
                <h4 style="margin:4px 0 0;color:#004D40;">R <?php echo e(number_format($payRun->total_gross, 2)); ?></h4>
            </div>
        </div>
        <div class="col-md-3">
            <div style="background:#fce4ec;border-left:4px solid #dc3545;padding:12px 16px;border-radius:4px;">
                <small style="color:#dc3545;">Total Deductions</small>
                <h4 style="margin:4px 0 0;color:#dc3545;">R <?php echo e(number_format($payRun->total_deductions, 2)); ?></h4>
            </div>
        </div>
        <div class="col-md-3">
            <div style="background:#e8f5e9;border-left:4px solid #28a745;padding:12px 16px;border-radius:4px;">
                <small style="color:#28a745;">Total Net Pay</small>
                <h4 style="margin:4px 0 0;color:#155724;font-weight:800;">R <?php echo e(number_format($payRun->total_net_pay, 2)); ?></h4>
            </div>
        </div>
    </div>

    <!-- Employee Payslip Cards -->
    <div class="row"><div class="col-12">
        <div class="card smartdash-form-card">
            <div class="card-header"><h4><i class="fas fa-users"></i> INDIVIDUAL PAYSLIPS</h4></div>
            <div class="card-body">
                <?php $__currentLoopData = $payRun->lines->sortBy(fn($l) => $l->employee->last_name ?? ''); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $line): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="payslip-card">
                    <div class="row align-items-center">
                        <div class="col-md-4">
                            <div class="payslip-emp-name"><?php echo e($line->employee->first_name ?? ''); ?> <?php echo e($line->employee->last_name ?? ''); ?></div>
                            <div class="payslip-detail">
                                #<?php echo e($line->employee->employee_number ?? ''); ?>

                                <?php if($line->employee->job_title): ?> — <?php echo e($line->employee->job_title); ?><?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-2" style="text-align:center;">
                            <small style="color:#999;">Gross</small><br>
                            <span style="font-weight:600;">R <?php echo e(number_format($line->gross_pay, 2)); ?></span>
                        </div>
                        <div class="col-md-2" style="text-align:center;">
                            <small style="color:#999;">Deductions</small><br>
                            <span style="font-weight:600;color:#dc3545;">R <?php echo e(number_format($line->total_deductions, 2)); ?></span>
                        </div>
                        <div class="col-md-2">
                            <div class="payslip-amount">R <?php echo e(number_format($line->net_pay, 2)); ?></div>
                            <small style="color:#28a745;float:right;">Net Pay</small>
                        </div>
                        <div class="col-md-2" style="text-align:right;">
                            <a href="<?php echo e(route('cimspayroll.payslips.view-single', $line->id)); ?>" class="btn button_master_edit" style="padding:4px 10px;font-size:11px;" target="_blank" title="View PDF in new tab"><i class="fa fa-eye"></i></a>
                            <a href="<?php echo e(route('cimspayroll.payslips.download-single', $line->id)); ?>" class="btn button_master_save" style="padding:4px 10px;font-size:11px;" title="Download PDF"><i class="fa fa-download"></i></a>
                        </div>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    </div></div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.default', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /usr/www/users/smartucbmh/application/Modules/CIMS_PAYROLL/Resources/views/payroll/payslips/preview.blade.php ENDPATH**/ ?>