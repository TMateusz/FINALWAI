<?php

    class Router{
        /**
         * routes: mapping path => ['controller' => ControllerName, 'action' => actionName]
         */
        private $routes = [
            '/gallery' => ['controller' => 'GalleryController', 'action' => 'index'],
            '/user'    => ['controller' => 'UserController', 'action' => 'index'],
        ];

        /**
         * Add a route. $action is optional and defaults to 'index'.
         */
        public function addRoute($url, $controller, $action = 'index'){
            $this->routes[$url] = ['url' => $url, 'controller' => $controller, 'action' => $action];
        }

        /**
         * Return a normalized route array for the given URL path or null if none.
         */
        public function getRoute($url){
            $path = parse_url($url, PHP_URL_PATH) ?? $url;
            // normalize trailing slash (keep root as '/')
            if ($path !== '/'){
                $path = rtrim($path, '/');
                if ($path === '') $path = '/';
            }

            if (!array_key_exists($path, $this->routes)){
                return null;
            }

            $route = $this->routes[$path];
            // backward compatibility: allow simple string controller values
            if (is_string($route)){
                return ['controller' => $route, 'action' => 'index'];
            }

            // ensure keys exist
            $controller = $route['controller'] ?? null;
            $action = $route['action'] ?? 'index';
            if ($controller === null) return null;

            return ['controller' => $controller, 'action' => $action];
        }
    }