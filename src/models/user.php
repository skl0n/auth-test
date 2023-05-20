<?php

class user
{
    private $id;
    private $email;
    private $password;
    private $secret;
    private $username;
    private $description;
    private $last_failed_login_attempt_date;
    private $failed_login_attempt_count;

    private static $profiles_logged_in = [];

    public function set_id($id)
    {
        $this->id = $id;
        return $this;
    }

    public function get_id()
    {
        return $this->id;
    }

    public function set_email($email)
    {
        $this->email = $email;
        return $this;
    }

    public function get_email()
    {
        return $this->email;
    }

    public function set_password($password)
    {
        $this->password = $password;
        return $this;
    }

    public function get_password()
    {
        return $this->password;
    }

    public function set_secret($secret)
    {
        $this->secret = $secret;
        return $this;
    }

    public function get_secret()
    {
        return $this->secret;
    }

    public function set_username($username)
    {
        $this->username = $username;
        return $this;
    }

    public function get_username()
    {
        return $this->username;
    }

    public function set_description($description)
    {
        $this->description = $description;
        return $this;
    }

    public function get_description()
    {
        return $this->description;
    }

    public function set_last_failed_login_attempt_date($last_failed_login_attempt_date)
    {
        $this->last_failed_login_attempt_date = $last_failed_login_attempt_date;
        return $this;
    }

    public function get_last_failed_login_attempt_date()
    {
        return $this->last_failed_login_attempt_date;
    }

    public function set_failed_login_attempt_count($failed_login_attempt_count)
    {
        $this->failed_login_attempt_count = $failed_login_attempt_count;
        return $this;
    }

    public function get_failed_login_attempt_count()
    {
        return $this->failed_login_attempt_count;
    }

    public function save()
    {
        if (!$this->get_id()) {
            return;
        }
        db::update_table(
            'user',
            [
                'username' => $this->get_username(),
                'secret' => $this->get_secret(),
                'password' => $this->get_password(),
                'description' => $this->get_description(),
                'last_failed_login_attempt_date' => $this->get_last_failed_login_attempt_date(),
                'failed_login_attempt_count' => $this->get_failed_login_attempt_count()
            ],
            ['id' => $this->get_id()]
        );
    }

    public static function find_one_by($params)
    {
        $user = null;
        $user_data = db::get_table('user', null, $params, 1);
        if ($user_data) {
            $user = self::set_user_object($user_data);
        }
        
        return $user;
    }

    public static function login_user($user)
    {
        $user->set_secret(md5(time()));
        $user->save();
        $cookie_value = $user->get_id() . '_' . $user->get_secret();
        $users = !empty($_COOKIE['users']) ? $_COOKIE['users'] : [];
        if (!in_array($cookie_value, $users)) {
            $users[] = $cookie_value;
            $key = count($users) - 1;
            setcookie("users[" . ($key) . "]", $cookie_value, 0, "/", "");
            $_COOKIE['users'][$key] = $cookie_value;
        }

        setcookie("current_user", $cookie_value, 0, "/", "");
    }

    public static function logout_user($user)
    {
        $current_user_data =  !empty($_COOKIE['current_user']) ? $_COOKIE['current_user'] : '';
        $users_data = !empty($_COOKIE['users']) ? $_COOKIE['users'] : [];
        $logged_in_cookie = '';
        foreach ($users_data as $key => $user_data) {
            list($user_id, $user_secret) = explode('_', $user_data);
            setcookie("users[" . $key. "]", "", 0, "/", "");
            if ($user_id === $user->get_id() && $user_secret === $user->get_secret()) {
                $logged_in_cookie = $user_data;
                unset($users_data[$key]);
                unset($_COOKIE['users'][$key]);
            }
        }

        $users_data = array_values($users_data);
        $_COOKIE['users'] = [];
        foreach ($users_data as $key => $user_data) {
            setcookie("users[" . $key. "]", $user_data, 0, "/", "");
            $_COOKIE['users'][$key] = $user_data;
        }
        if ($current_user_data == $logged_in_cookie && $users_data) {
            $cookie_value = array_shift($users_data);
            setcookie("current_user", $cookie_value, 0, "/", "");
            $_COOKIE['current_user'] = $cookie_value;
        }
    }

    public static function get_user_logged_profiles($force = false)
    {
        if (self::$profiles_logged_in && !$force) {
            return self::$profiles_logged_in;
        }
        self::$profiles_logged_in = [];
        $users_data = !empty($_COOKIE['users']) ? $_COOKIE['users'] : [];
        foreach ($users_data as $user_data) {
            list($user_id, $user_secret) = explode('_', $user_data);
            $user = self::find_one_by(['id' => $user_id, 'secret' => $user_secret]);
            if ($user) {
                self::$profiles_logged_in[] = $user;
            }
        }
        return self::$profiles_logged_in;
    }

    private static function set_user_object($data)
    {
        $user = new self();
        $user->set_id($data['id'])
            ->set_email($data['email'])
            ->set_password($data['password'])
            ->set_secret($data['secret'])
            ->set_username($data['username'])
            ->set_description($data['description'])
            ->set_last_failed_login_attempt_date(new DateTime($data['last_failed_login_attempt_date']))
            ->set_failed_login_attempt_count($data['failed_login_attempt_count'])
        ;
        return $user;
    }
}
