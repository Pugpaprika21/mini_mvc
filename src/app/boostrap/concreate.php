<?php

namespace App\Core;

class Router
{
    protected array $routes = [];

    public function add(string $pattern, callable $handler): void
    {
        $this->routes[$pattern] = $handler;
    }

    public function dispatch(string $uri): void
    {
        foreach ($this->routes as $pattern => $handler) {
            if (preg_match($pattern, $uri, $matches)) {
                array_shift($matches); 
                call_user_func_array($handler, $matches);
                return;
            }
        }

        http_response_code(404);
        echo "404 Not Found";
    }
}
