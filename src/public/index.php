<?php

require_once __DIR__ . '/../app/Core/Router.php';

use App\Core\Router;

$router = new Router();

$router->add('#^/api/v1/test', function () {
    echo "test";
});

$router->group('/api/v1/user', function (Router $router) {
    $router->add('/list', function () {
        echo "User list";
    });

    $router->add('#/get/(\d+)#', function ($id) {
        echo "Get user ID = $id";
    });

    $router->add('#/(\d+)/edit#', function ($id) {
        echo "Edit user ID = $id";
    });
});

$router->group('/api/v1/product', function (Router $router) {
    $router->add('#/show/(\d+)#', function ($id) {
        echo "Show product ID = $id";
    });
});

$route = $_GET['route'] ?? '/';
$router->dispatch($route);
