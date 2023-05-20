<?php

class template
{
    public $data = [];

    public function view($template, $data = [], $return = false)
    {
        $data = $data ?: $this->data;
        extract($data);
        ob_start();
        require SRC_DIR . '/templates/' . $template . '.php';
        $view = ob_get_clean();
        if ($return) {
            return $view;
        }
        echo $view;
    }
}