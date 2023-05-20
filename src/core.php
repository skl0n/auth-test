<?php

include_once SRC_DIR . '/libs/config.php';
include_once SRC_DIR . '/libs/router.php';
include_once SRC_DIR . '/libs/db.php';
include_once SRC_DIR . '/models/user.php';

class Core
{
    private static $instance = null;
    private $services = [];

    public static function get_instance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function run()
    {
        $config = new config();
        $this->set_service('config', $config);
        $router = new router();
        db::init($config->db);

        $controller_name = $router->get_controller();
        $controller_path = SRC_DIR . '/controllers/';
        if (file_exists($controller_path . $controller_name . '_controller.php')) {
            include_once $controller_path . $controller_name . '_controller.php';
            $controller_class = "{$controller_name}_controller";
            $controller = new $controller_class();
            $action = $router->get_action();
            if (method_exists($controller, $action)) {
                $controller->$action();
                return;
            }

        }

        include_once $controller_path . 'not_found_controller.php';
        $controller = new not_found_controller();
        $controller->index();
    }

    public function set_service($name, $variable)
    {
        $this->services[$name] = $variable;
        return $this;
    }

    public function get_service($name)
    {
        return $this->services[$name] ?: null;
    }
}