<?php
include_once __DIR__ . '/../Core/View.php';
include_once __DIR__ . '/../Core/session_helpers.php';

    abstract class BaseController{
        protected function render($viewName, $data = []){
            return ['view' => $viewName, 'data' => $data];
        }

        protected function redirect($url){
            return 'redirect:' . $url;
        }
    }

