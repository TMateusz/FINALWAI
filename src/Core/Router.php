<?php

    class Router{
        private $routes = [
            '/gallery' => 'GalleryController',
            '/user' => 'UserController',
            // Dodaj więcej tras według potrzeb
        ];

        public function addRoute($url, $controller, $action){
            $this->routes[$url] = ['url' => $url, 'controller' => $controller, 'action' => $action];
        }
        public function getRoute($url){
            parse_url($url, PHP_URL_PATH);
            if (array_key_exists($url, $this->routes)){
                return $this->routes[$url];
            }else{
                return null;
            }
        }
    }