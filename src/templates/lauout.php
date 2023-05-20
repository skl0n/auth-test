<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= $title ?? '';?></title>

    <meta content="<?= $csrf_parameter_name ?>" name="csrf_parameter_name" />
    <meta content="<?= $csrf_token ?>" name="<?= $csrf_parameter_name ?>" />

    <link rel="stylesheet" type="text/css" href="/assets/css/app.css">
    <link rel="stylesheet" type="text/css" href="/assets/css/button.css">
    <link rel="stylesheet" type="text/css" href="/assets/css/form.css">
    <link rel="stylesheet" type="text/css" href="/assets/css/page/404.css">
    <link rel="stylesheet" type="text/css" href="/assets/css/page/login.css">
    <link rel="stylesheet" type="text/css" href="/assets/css/page/profile.css">
</head>
<body class="<?= $body_class ?? ''?>">
    <div class="layout_container">
        <header>
            <?php include 'partials/header.php' ?>
        </header>
        <div class="content_container">
            <div class="container">
                <?php echo $content ?>
            </div>
        </div>
        <footer>
            <div class="container">
                (c) <?php echo date('Y'); ?>
            </div>
        </footer>
    </div>

    <script>
        var base_url = "<?= core::get_instance()->get_service('config')->url; ?>"
    </script>
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="/assets/js/app.js"></script>
</body>
</html>