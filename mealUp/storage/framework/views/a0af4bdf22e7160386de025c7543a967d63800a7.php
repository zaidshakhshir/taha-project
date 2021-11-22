<?php $__env->startSection('title','Delivery Person Setting'); ?>

<?php $__env->startSection('content'); ?>
<section class="section">
    <?php if(Session::has('msg')): ?>
        <?php echo $__env->make('layouts.msg', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php endif; ?>
    <div class="section-header">
        <h1><?php echo e(__('Delivery person settings')); ?></h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="<?php echo e(url('admin/home')); ?>"><?php echo e(__('Dashboard')); ?></a></div>
            <div class="breadcrumb-item active"><a href="<?php echo e(url('admin/setting')); ?>"><?php echo e(__('Setting')); ?></a></div>
            <div class="breadcrumb-item"><?php echo e(__('Delivery person setting')); ?></div>
        </div>
    </div>
    <div class="section-body">
        <h2 class="section-title"><?php echo e(__('Delivery person Setting management')); ?></h2>
        <p class="section-lead"><?php echo e(__('Delivery person setting')); ?></p>
        <form action="<?php echo e(url('admin/update_delivery_person_setting')); ?>" method="post">
            <?php echo csrf_field(); ?>
            <div class="card p-2">
                <div class="card-body">
                    <h5 class="mt-3"><?php echo e(__('Delivery person setting')); ?></h5>
                    <hr>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label for="driver assign km"><?php echo e(__('Driver dashboard auto refresh(In seconds)')); ?></label>
                            <input type="number" min=1 required name="driver_auto_refrese" class="form-control" value="<?php echo e($general_setting->driver_auto_refrese); ?>">
                            <?php $__errorArgs = ['driver_auto_refrese'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <span class="custom_error" role="alert">
                                    <strong><?php echo e($message); ?></strong>
                                </span>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card p-2">
                <div class="card-body">
                    <h5 class="mt-3"><?php echo e(__('Delivery person Vehical')); ?></h5>
                    <hr>
                    <table class="table driver_vehical_table">
                        <thead>
                            <tr>
                                <th><?php echo e(__('Vehicle Type')); ?></th>
                                <th><?php echo e(__('License Required')); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if($general_setting->driver_vehical_type != null): ?>
                                <?php
                                    $driver_vehical_types = json_decode($general_setting->driver_vehical_type)
                                ?>
                                <?php $__currentLoopData = $driver_vehical_types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $driver_vehical_type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td>
                                        <input type="text" name="vehical_type[]" value="<?php echo e($driver_vehical_type->vehical_type); ?>" class="form-control" required>
                                    </td>
                                    <td>
                                        <select name="license[]" class="form-control">
                                            <option value="yes" <?php echo e($driver_vehical_type->license == 'yes' ? 'selected' : ''); ?>><?php echo e(__('Yes')); ?></option>
                                            <option value="no" <?php echo e($driver_vehical_type->license == 'no' ? 'selected' : ''); ?>><?php echo e(__('no')); ?></option>
                                        </select>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-danger removebtn"><i class="fas fa-times"></i></button>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php else: ?>
                                <tr>
                                    <td>
                                        <input type="text" name="vehical_type[]" class="form-control" required>
                                    </td>
                                    <td>
                                        <select name="license[]" class="form-control">
                                            <option value="yes"><?php echo e(__('Yes')); ?></option>
                                            <option value="no"><?php echo e(__('no')); ?></option>
                                        </select>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-danger removebtn"><i class="fas fa-times"></i></button>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                    <div class="text-center mt-3">
                        <button type="button" class="btn btn-primary" onclick="add_drivervehical_field()"><?php echo e(__('Add Field')); ?></button>
                    </div>
                </div>

                <div class="card-body">
                    <h5 class="mt-3"><?php echo e(__('Delivery person Earning')); ?></h5>
                    <hr>
                    <table class="table driver_earning_table">
                        <thead>
                            <tr>
                                <th><?php echo e(__('Minimum KM')); ?></th>
                                <th><?php echo e(__('Maximum KM')); ?></th>
                                <th><?php echo e(__('Delivery Person charges')); ?>(<?php echo e($currency_symbol); ?>)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if($general_setting->driver_earning != null): ?>
                                <?php
                                    $driver_earnings = json_decode($general_setting->driver_earning)
                                ?>
                                <?php $__currentLoopData = $driver_earnings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $driver_earning): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td>
                                        <input type="number" min=1 name="min_km[]" value="<?php echo e($driver_earning->min_km); ?>" class="form-control" required>
                                    </td>
                                    <td>
                                        <input type="number" min=1 name="max_km[]" value="<?php echo e($driver_earning->max_km); ?>" class="form-control" required>
                                    </td>
                                    <td>
                                        <input type="number" min=1 name="charge[]" value="<?php echo e($driver_earning->charge); ?>" class="form-control" required>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-danger removebtn"><i class="fas fa-times"></i></button>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php else: ?>
                                <tr>
                                    <td>
                                        <input type="number" name="min_km[]" min=1 class="form-control" required>
                                    </td>
                                    <td>
                                        <input type="number" name="max_km[]" min=1 class="form-control" required>
                                    </td>
                                    <td>
                                        <input type="number" name="charge[]" min=1 class="form-control" required>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-danger removebtn"><i class="fas fa-times"></i></button>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                    <div class="text-center mt-3">
                        <button type="button" class="btn btn-primary" onclick="add_driverearning_field()"><?php echo e(__('Add Field')); ?></button>
                    </div>
                </div>

                <div class="card-body">
                    <h5 class="mt-3"><?php echo e(__('Cancel reasons')); ?></h5>
                    <hr>
                    <table class="table cancel_reason">
                        <thead>
                            <tr>
                                <th><?php echo e(__('Cancel reason')); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if($general_setting->cancel_reason != null): ?>
                                <?php
                                    $cancel_reasons = json_decode($general_setting->cancel_reason)
                                ?>
                                <?php $__currentLoopData = $cancel_reasons; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cancel_reason): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td>
                                        <input type="text" name="cancel_reason[]" value="<?php echo e($cancel_reason); ?>" class="form-control" required>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-danger removebtn"><i class="fas fa-times"></i></button>
                                    </td>
                                </tr>

                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php else: ?>
                                <tr>
                                    <td>
                                        <input type="text" name="cancel_reason[]" class="form-control" required>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-danger removebtn"><i class="fas fa-times"></i></button>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                    <div class="text-center mt-3">
                        <button type="button" class="btn btn-primary" onclick="add_cancel_reason()"><?php echo e(__('Add Field')); ?></button>
                    </div>
                </div>
                <div class="text-center">
                    <button type="submit" class="btn btn-primary mt-5"><?php echo e(__('save')); ?></button>
                </div>
            </div>

        </form>
    </div>
</section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app',['activePage' => 'setting'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\laravel\mealUp\resources\views/admin/setting/delivery_person_setting.blade.php ENDPATH**/ ?>