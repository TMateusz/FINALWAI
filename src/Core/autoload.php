<?php
// Simple project autoloader — searches common src folders for class files.
spl_autoload_register(function ($class) {
    $base = __DIR__ . '/../';
    $paths = [
        $base . 'Controllers/',
        $base . 'Business/',
        $base . 'Models/',
        $base . 'Core/',
        $base . 'static/',
    ];

    foreach ($paths as $p) {
        $file = $p . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            return true;
        }
    }

    return false;
});
