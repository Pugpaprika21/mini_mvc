<?php

namespace App\Foundation\Router {

    use Exception;

    interface RouteInterface
    {
        public function group(string $prefix, callable $callback): void;
        public function get(string $pattern, callable $handler, array $middlewares = []): void;
        public function post(string $pattern, callable $handler, array $middlewares = []): void;
        public function put(string $pattern, callable $handler, array $middlewares = []): void;
        public function delete(string $pattern, callable $handler, array $middlewares = []): void;
    }

    interface RouteGroupInterface extends RouteInterface {}

    final class Router implements RouteGroupInterface
    {
        private array $routes = [];
        private string $prefix = "";
        
        private const Get = "GET";
        private const Post = "POST";
        private const Put = "PUT";
        private const Delete = "DELETE";
        
        public function group(string $prefix, callable $callback): void
        {
            $previousPrefix = $this->prefix;
            $this->prefix .= $prefix;
            $callback($this);
            $this->prefix = $previousPrefix;
        }
        public function get(string $pattern, callable $handler, array $middlewares = []): void
        {
            $this->addHttpMethod(self::Get);
            $this->add($pattern, $handler, $middlewares);
        }

        public function post(string $pattern, callable $handler, array $middlewares = []): void
        {
            $this->addHttpMethod(self::Post);
            $this->add($pattern, $handler, $middlewares);
        }

        public function put(string $pattern, callable $handler, array $middlewares = []): void
        {
            $this->addHttpMethod(self::Put);
            $this->add($pattern, $handler, $middlewares);
        }

        public function delete(string $pattern, callable $handler, array $middlewares = []): void
        {
            $this->addHttpMethod(self::Delete);
            $this->add($pattern, $handler, $middlewares);
        }

        public function dispatch(string $uri): void
        {
            foreach ($this->routes as $route) {
                if (preg_match($route["regex"], $uri, $matches)) {
                    array_shift($matches);
                    $params = array_combine($route["paramNames"], $matches);

                    $middlewareChain = array_reverse($route["middlewares"] ?? []);
                    $next = function () use ($route, $params) {
                        call_user_func_array($route["handler"], $params);
                    };

                    foreach ($middlewareChain as $middleware) {
                        $current = $next;
                        $next = function () use ($middleware, $params, $current) {
                            return $middleware($params, $current);
                        };
                    }

                    $next();
                    return;
                }
            }

            throw new Exception("404 Not Found", 404);
        }

        private function convertToRegex(string $pattern, ?array &$paramNames = []): string
        {
            $paramNames = [];
            $regex = preg_replace_callback("#\{(\w+)\}#", function ($matches) use (&$paramNames) {
                $paramNames[] = $matches[1];
                return "([^/]+)";
            }, $pattern);
            return "#^" . $regex . "$#";
        }

        private function add(string $pattern, callable $handler, array $middlewares = []): void
        {
            $fullPattern = $this->prefix . $pattern;
            $regex = $this->convertToRegex($fullPattern, $paramNames);

            $this->routes[] = [
                "regex" => $regex,
                "paramNames" => $paramNames,
                "handler" => $handler,
                "middlewares" => $middlewares,
            ];
        }

        private function addHttpMethod(string $method): void
        {
            if ($_SERVER["REQUEST_METHOD"] !== $method) {
                throw new Exception("Invalid HTTP method. Expected {$method}", 405);
            }
        }
    }
}
