<?php
session_start();

require_once __DIR__ . '/src/Core/Router.php';
require_once __DIR__ . '/src/Core/Dispatcher.php';
require_once __DIR__ . '/src/Core/Database.php';

$router = new Router();

$router->addRoute('/', 'GalleryController', 'index');
$router->addRoute('/gallery', 'GalleryController', 'index');
$router->addRoute('/login', 'AuthController', 'login');
$router->addRoute('/logout', 'AuthController', 'logout');
$router->addRoute('/register', 'AuthController', 'register');
$router->addRoute('/upload', 'UploadController', 'index');
$router->addRoute('/search', 'SearchController', 'index');
$router->addRoute('/cart', 'CartController', 'index');
$router->addRoute('/cart/remove', 'CartController', 'remove');
$router->addRoute('/cart/save', 'GalleryController', 'saveCart');

$actionUrl = isset($_GET['action']) ? $_GET['action'] : '/';
$route = $router->getRoute($actionUrl);

$dispatcher = new Dispatcher($router);
$dispatcher->dispatch($actionUrl);
