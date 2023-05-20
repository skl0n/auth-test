<?php

define('BASE_DIR', dirname(__FILE__));
define('SRC_DIR', BASE_DIR . '/src');

@session_start();

include SRC_DIR . '/core.php';

$core = Core::get_instance();

$core->run();
