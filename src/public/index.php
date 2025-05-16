<?php

declare(strict_types=1);

use App\Foundation\Router\RouteGroupInterface;
use App\Foundation\Router\Router as AppRouter;

require_once __DIR__ . "/../app/Foundation/Router/Router.php";

$app = new AppRouter();

$authMiddleware = function ($params, $next) {
    if ($_GET['token'] ?? '' !== 'secret') {
        echo "Denied\n";
        return;
    }
    echo "Middleware Passed\n";
    $next();
};

// http://localhost:9090/?route=/api/v1/get
$app->get("/user/{userId}/post/{postId}", function ($userId, $postId) {
    echo "User ID: $userId, Post ID: $postId";
});

$app->group("/api/v1", function (RouteGroupInterface $group) use($authMiddleware) {
    $group->get("/get", function () {
        echo "AAAA";
    }, [$authMiddleware]);
});

try {
    $route = filter_var($_GET["route"] ?? "/", FILTER_SANITIZE_URL);
    $app->dispatch($route);
} catch (Exception $e) {
    echo json_encode([
        "message" => $e->getMessage(),
        "code_error" => $e->getCode(),
    ]);
}
