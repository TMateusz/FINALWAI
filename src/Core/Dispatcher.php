<?php

class Dispatcher{
    private $router;

    public function __construct($router){
        $this->router = $router;
    }

    public function dispatch($url){
        $route = $this->router->getRoute($url);
        if ($route == null){
            echo "404 Not Found";;
            return;
        }

        $controllerName = $route['controller'];
        $actionName = $route['action'];

        // Rely on autoloader to load controller class file
        if (!class_exists($controllerName)){
            echo "Controller class not found: {$controllerName}";
            return;
        }
        $controller = new $controllerName();
        if (!method_exists($controller, $actionName)){
            echo "Action not found in controller.";
            return;
        }
        $result = $controller->$actionName(); // co to robi? - wywołuje metodę akcji kontrolera i przechowuje wynik w $result. Co zawiera wynik? 
        // Jeśli $result jest stringiem zaczynającym się od 'redirect:':

        // 
        if ($result !== null) {

            if (is_string($result) && str_starts_with($result, 'redirect:')) {
                $redirectUrl = substr($result, 9); // Usuń 'redirect:' z początku
                header("Location: " . $redirectUrl); 
                exit();
            }else if (is_array($result) && array_key_exists('view', $result)) { // jak sprwawdzić czy jest tablicą z kluczem view? - 
                $viewFile = __DIR__ . "/../Views/" . $result['view'] . ".php";
                View::render($result['view'], $result['data'] ); 
            } else {
                echo $result; // Wyświetl wynik jako string
            }

        }
        return $route;
    }
}