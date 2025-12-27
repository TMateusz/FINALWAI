<?php
$sessionStarted = session_status() !== PHP_SESSION_ACTIVE;
if ($sessionStarted) session_start();

require_once __DIR__ . '/src/Core/autoload.php';

$router = new Router();

$router->addRoute('/', 'GalleryController', 'index');
$router->addRoute('/gallery', 'GalleryController', 'index');
$router->addRoute('/login', 'AuthController', 'login');
$router->addRoute('/logout', 'AuthController', 'logout');
$router->addRoute('/register', 'AuthController', 'register');
$router->addRoute('/upload', 'UploadController', 'index');
$router->addRoute('/search', 'SearchController', 'inde
x');
$router->addRoute('/cart', 'CartController', 'index');
$router->addRoute('/cart/remove', 'CartController', 'remove');
$router->addRoute('/cart/save', 'GalleryController', 'saveCart');
$router->addRoute('/cart/update', 'GalleryController', 'updateCart');

$actionUrl = isset($_GET['action']) ? $_GET['action'] : '/';
$route = $router->getRoute($actionUrl);

$dispatcher = new Dispatcher($router);
$dispatcher->dispatch($actionUrl);
