<?php

include_once 'abstract_controller.php';

class auth_controller extends abstract_controller
{
    public function login()
    {
        $username = !empty($_POST['username']) ? trim($_POST['username']) : null;
        $password = !empty($_POST['password']) ? trim($_POST['password']) : '';

        $error = '';
        if ($username) {
            $user = user::find_one_by(['username' => $username]);
            if ($user) {
                $login_attempts_limit = 5;
                $block_time_minutes = 15;
                if (
                    $user->get_last_failed_login_attempt_date()
                    && $user->get_last_failed_login_attempt_date() > new \DateTime("-$block_time_minutes minutes")) {
                    if ($user->get_failed_login_attempt_count() >= $login_attempts_limit) {
                        $error = str_replace(
                            '{block_time}',
                            $block_time_minutes,
                            'Your account has been disabled for {block_time} minutes because you have failed to log in correctly too many times'
                        );
                    }
                } else {
                    $user->set_failed_login_attempt_count(0);
                }

                $success_login_user = user::find_one_by(['username' => $username, 'password' => md5($password)]);
                if (!$success_login_user) {
                    $user->set_last_failed_login_attempt_date(new \DateTime('now'));
                    $failed_login_attempt_count = $user->get_failed_login_attempt_count();
                    $user->set_failed_login_attempt_count($failed_login_attempt_count++);
                    $user->save();
                    $error = 'Wrong password';
                } else {
                    user::login_user($user);
                }
            } else {
                $error = 'Wrong username';
            }
        } else {
            $error = 'Username field is required';
        }

        if ($error) {
            $status = 'error';
            $view = $this->view('partials/login_form', [
                'error' => $error,
                'username' => $username,
            ], true);
        } else {
            $status = 'ok';
            $view = $this->view('partials/profile', ['user' => $user], true);
        }

        $this->json_response([
            'view' => $view,
            'status' => $status
        ]);
    }

    public function logout()
    {
        if (!$this->user) {
            return;
        }
        $logout_user_id = $_POST['user_id'] ?? null;
        if (!$logout_user_id) {
            return;
        }

        $logout_user = $this->get_user_logged_by_id($logout_user_id);
        if ($logout_user) {
            user::logout_user($logout_user);

            $this->json_response([
                'view' => $this->view('partials/login_form', [], true),
                'status' => 'ok'
            ]);
        }
    }

    public function change_current_user()
    {
        if (!$this->user) {
            return;
        }
        $change_user_id = $_POST['change_user_id'] ?? null;
        if (!$change_user_id) {
            return;
        }

        $change_user = $this->get_user_logged_by_id($change_user_id);
        if ($change_user) {
            user::logout_user($change_user);
            user::login_user($change_user);
            $this->json_response([
                'view' => $this->view('partials/profile', ['user' => $change_user], true),
                'status' => 'ok'
            ]);
        }
    }

    private function get_user_logged_by_id($user_id)
    {
        $users = user::get_user_logged_profiles();
        foreach ($users as $user) {
            if ($user->get_id() === $user_id) {
                return $user;
            }
        }
        return null;
    }
}
