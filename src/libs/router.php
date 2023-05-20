<?php

class router
{
    private $controller = 'main';
    private $action = 'index';

    public function __construct()
    {
        $url = $_SERVER['REQUEST_SCHEME']. '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

        $core = core::get_instance();
        $config = $core->get_service('config');
        $uri = str_replace($config->url, '', $url);
        $uri = ltrim( str_replace('?' . $_SERVER['QUERY_STRING'], '', $uri), '/' );
        $uri = explode('/', $uri);
        if (isset($uri[0]) && !empty($uri[0])) {
            $this->controller = $uri[0];
        }
        if ( isset($uri[1]) && !empty($uri[1]) ) {
            $this->action = str_replace('-', '_', $uri[1]);
        }
    }

    public function get_controller()
    {
        return $this->controller;
    }

    public function get_action()
    {
        return $this->action;
    }
}

function redirect($uri = '', $http_response_code = 302)
{
    header("Location: ". $uri, TRUE, $http_response_code);
    exit;
}