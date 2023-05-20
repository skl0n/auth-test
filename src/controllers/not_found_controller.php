<?php

include_once 'abstract_controller.php';

class not_found_controller extends abstract_controller
{
    function index()
    {
        header("HTTP/1.0 404 Not Found");
        header("HTTP/1.1 404 Not Found");
        header("Status: 404 Not Found");

        $this->data['title'] = 'Page Not Found';
        $this->view('404');
    }
}