<script>
var msg = "<?php echo Session::get('msg'); ?>"
$(window).on('load', function()
{
    iziToast.success({
        message: msg,
        position: 'topRight'
    });
});
</script><?php /**PATH C:\xampp\htdocs\laravel\mealUp\resources\views/layouts/msg.blade.php ENDPATH**/ ?>