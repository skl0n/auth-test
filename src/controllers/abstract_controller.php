<?php

include SRC_DIR . '/libs/template.php';

class abstract_controller extends template
{
    private $csrf_parameter_name = 'csrf_token';
    private $csrf_token = '';
    protected $skip_csrf_verification = false;
    public $user = null;

    public function __construct()
    {
        if (!$this->csrf_validation()) {
            echo 'CSRF attack detected';
            exit;
        }
        $this->check_user_logged_in();
        $this->data['csrf_parameter_name'] = $this->csrf_parameter_name;
        $this->data['csrf_token'] = $this->get_csrf_token();
    }

    protected function json_response($data)
    {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data);
        exit;
    }

    private function check_user_logged_in()
    {
        $current_user = $_COOKIE['current_user'] ?? '';
        if (!$current_user) {
            return;
        }
        list($current_user_id, $current_user_secret) = explode('_', $current_user);
        $user = array_filter(user::get_user_logged_profiles(), function($one) use ($current_user_id, $current_user_secret) {
            return $one->get_id() == $current_user_id && $one->get_secret() === $current_user_secret;
        });

        if ($user) {
            $this->user = $this->data['user'] = array_values($user)[0];
        }
    }

    private function get_csrf_token()
    {
        if (!empty($_SESSION[$this->csrf_parameter_name])) {
            return $this->csrf_token = $_SESSION[$this->csrf_parameter_name];
        }

        $_SESSION[$this->csrf_parameter_name] = $this->csrf_token = md5(openssl_random_pseudo_bytes(16));
        return $this->csrf_token;
    }

    private function csrf_validation() {
        if ($this->skip_csrf_verification) return true;

        if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
            if (
                !empty($_POST[$this->csrf_parameter_name])
                && $_POST[$this->csrf_parameter_name] === $this->get_csrf_token()
            ) {
                return true;
            }

            redirect($_SERVER['REQUEST_URI']);
        }

        return true;
    }
}
