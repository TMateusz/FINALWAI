<?php

    class View{
        public static function render($viewName, $data = []){
            extract ($data);
            
            if (!file_exists(__DIR__ . "/../views/{$viewName}.php")){
                echo "View file not found.";
                return;
            }
            
            ob_start();
            include __DIR__ . "/../views/{$viewName}.php";
            $content = ob_get_clean();
            
            $layoutPath = __DIR__ . '/../views/layout.php';
            if (file_exists($layoutPath)) {
                include $layoutPath;
            } else {
                echo $content;
            }
        }
    }