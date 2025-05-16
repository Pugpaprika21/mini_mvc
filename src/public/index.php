<?php

declare(strict_types=1);

use PPPk\Factory\AppBuilder;
use PPPk\Router\RouteGroupInterface;

require_once __DIR__ . "/../app/Foundation/AppBuilder.php";

class UserController
{
    public function index($id)
    {
        echo $id;
    }
}

$app = new AppBuilder();

$router = $app->useRouter();

$router->get("/test/{id}", [UserController::class, "index"]);
$router->get("/user/{userId}", [UserController::class, "index"]);
$router->get("/product/{productId}", [UserController::class, "index"]);

$router->get("/user/{userId}/post/{postId}", function ($userId, $postId) {
    echo "User ID: $userId, Post ID: $postId";
});

$router->group("/api/v1", function (RouteGroupInterface $group) {
    $group->get("/get", function () {
        echo "AAAA";
    });
});

$route = filter_var($_GET["route"] ?? "/", FILTER_SANITIZE_URL);
$router->dispatch($route);
