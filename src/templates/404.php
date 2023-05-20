<?php ob_start() ?>
<div class="not_found">
    <div>
        404 <span>Page not found</span>
    </div>
</div>
<?php $content = ob_get_clean(); ?>

<?php include_once 'lauout.php'; ?>
