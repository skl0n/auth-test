<?php ob_start() ?>

<?php include 'partials/login_form.php'?>

<?php $content = ob_get_clean(); ?>

<?php include_once 'lauout.php'; ?>
