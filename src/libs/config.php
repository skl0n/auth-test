<?php

class config
{
    private $config = [];

    public function __construct()
    {
        $config_file = BASE_DIR . '/config/config.php';
        if (file_exists($config_file)) {
            $this->loadFile($config_file);
        }
    }

    public function __isset($key)
    {
        return isset($this->config[$key]);
    }

    public function __get($key)
    {
        return $this->config[$key] ?? null;
    }

    private function loadFile($file)
    {
        $config = [];
        include $file;
        $this->config = & $config;
    }
}
