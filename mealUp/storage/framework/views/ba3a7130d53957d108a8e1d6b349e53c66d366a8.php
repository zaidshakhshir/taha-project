<?php $__env->startSection('title','Setting'); ?>

<?php $__env->startSection('content'); ?>

<section class="section">
    <?php if(Session::has('msg')): ?>
        <?php echo $__env->make('layouts.msg', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php endif; ?>
    <div class="section-header">
        <h1><?php echo e(__('Settings')); ?></h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="<?php echo e(url('admin/home')); ?>"><?php echo e(__('Dashboard')); ?></a></div>
            <div class="breadcrumb-item"><?php echo e(__('Settings')); ?></div>
        </div>
    </div>
    <div class="section-body">
        <h2 class="section-title"><?php echo e(__('Settings and Management')); ?></h2>
        <p class="section-lead"><?php echo e(__('Set your panel up to date with general settings')); ?></p>
        <div class="row">
            <div class="col-lg-6">
                <div class="card card-large-icons">
                    <div class="card-icon bg-primary text-white">
                        <i class="fas fa-cog"></i>
                    </div>
                    <div class="card-body">
                        <h4><?php echo e(__('General')); ?></h4>
                        <p><?php echo e(__('General settings such as, site title, site description, address and so on.')); ?></p>
                        <a href="<?php echo e(url('admin/general_setting')); ?>" class="card-cta"><?php echo e(__('Change Setting ')); ?><i class="fas fa-chevron-right"></i></a>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card card-large-icons">
                    <div class="card-icon bg-primary text-white">
                        <i class="fas fa-money-check-alt"></i>
                    </div>
                    <div class="card-body">
                        <h4><?php echo e(__('Payment Setting')); ?></h4>
                        <p><?php echo e(__('Change the payment modes for the transaction.')); ?></p>
                        <a href="<?php echo e(url('admin/payment_setting')); ?>" class="card-cta"><?php echo e(__('Change Setting')); ?>

                            <i class="fas fa-chevron-right"></i></a>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card card-large-icons">
                    <div class="card-icon bg-primary text-white">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <div class="card-body">
                        <h4><?php echo e(__('Email')); ?></h4>
                        <p><?php echo e(__('Email SMTP settings, notifications and others related to email.')); ?></p>
                        <a href="<?php echo e(url('admin/notification_setting')); ?>" class="card-cta"><?php echo e(__('Change Setting')); ?><i
                                class="fas fa-chevron-right"></i></a>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card card-large-icons">
                    <div class="card-icon bg-primary text-white">
                        <i class="fas fa-power-off"></i>
                    </div>
                    <div class="card-body">
                        <h4><?php echo e(__('System')); ?></h4>
                        <p><?php echo e(__('Andriod ane IOS version settings.')); ?></p>
                        <a href="<?php echo e(url('admin/version_setting')); ?>" class="card-cta"><?php echo e(__('Change Setting')); ?> <i class="fas fa-chevron-right"></i></a>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card card-large-icons">
                    <div class="card-icon bg-primary text-white">
                        <i class="far fa-file-alt"></i>
                    </div>
                    <div class="card-body">
                        <h4><?php echo e(__('Static Pages')); ?></h4>
                        <p><?php echo e(__('Bussiness static pages like help about us privacy policy etc.')); ?></p>
                        <a href="<?php echo e(url('admin/static_pages')); ?>" class="card-cta"><?php echo e(__('Change Setting')); ?> <i
                                class="fas fa-chevron-right"></i></a>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card card-large-icons">
                    <div class="card-icon bg-primary text-white">
                        <i class="fas fa-sort"></i>
                    </div>
                    <div class="card-body">
                        <h4><?php echo e(__('Order Related settings')); ?></h4>
                        <p><?php echo e(__('Order related settings.')); ?></p>
                        <a href="<?php echo e(url('admin/order_setting')); ?>" class="card-cta"><?php echo e(__('Change Setting')); ?> <i
                                class="fas fa-chevron-right"></i></a>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card card-large-icons">
                    <div class="card-icon bg-primary text-white">
                        <i class="fab fa-red-river"></i>
                    </div>
                    <div class="card-body">
                        <h4><?php echo e(__('Delivery person setting')); ?></h4>
                        <p><?php echo e(__('Bussiness static pages like help about us privacy policy etc.')); ?></p>
                        <a href="<?php echo e(url('admin/delivery_person_setting')); ?>" class="card-cta"><?php echo e(__('Change Setting')); ?> <i
                                class="fas fa-chevron-right"></i></a>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card card-large-icons">
                    <div class="card-icon bg-primary text-white">
                        <i class="fas fa-users-cog"></i>
                    </div>
                    <div class="card-body">
                        <h4><?php echo e(__('User / vendor Verification setting')); ?></h4>
                        <p><?php echo e(__('Bussiness static pages like help about us privacy policy etc.')); ?></p>
                        <a href="<?php echo e(url('admin/verification_setting')); ?>" class="card-cta"><?php echo e(__('Change Setting')); ?> <i class="fas fa-chevron-right"></i></a>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card card-large-icons">
                    <div class="card-icon bg-primary text-white">
                        <i class="fab fa-elementor"></i>
                    </div>
                    <div class="card-body">
                        <h4><?php echo e(__('License Code')); ?></h4>
                        <p><?php echo e(__('Bussiness License name and code.')); ?></p>
                        <a href="<?php echo e(url('admin/license_setting')); ?>" class="card-cta"><?php echo e(__('Change Setting')); ?> <i class="fas fa-chevron-right"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app',['activePage' => 'setting'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\laravel\mealUp\resources\views/admin/setting/setting.blade.php ENDPATH**/ ?>