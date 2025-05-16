<?php

declare(strict_types=1);

use App\Factory\AppBuilder;
use App\Foundation\Router\RouteGroupInterface;

require_once __DIR__ . "/../app/Foundation/Router/Router.php";

class UserController
{
    public function index($id)
    {
        echo $id;
    }
}
// http://localhost:9090/?route=/api/v1/get
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

try {
    $route = filter_var($_GET["route"] ?? "/", FILTER_SANITIZE_URL);
    $router->dispatch($route);
} catch (Exception $e) {
    echo json_encode([
        "message" => $e->getMessage(),
        "code_error" => $e->getCode(),
    ]);
}
