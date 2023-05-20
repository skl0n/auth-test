<?php

include 'abstract_controller.php';

class main_controller extends abstract_controller
{
    public function index()
    {
        $this->user = $this->data['user'] = null;
        $this->data['body_class'] = 'login_page';
        $this->view('login');
    }
}