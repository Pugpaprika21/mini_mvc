<?php

/* @create by Pug */

namespace PPPk\Factory {

    use PPPk\Router\RouteInterface;
    use PPPk\Router\Router;

    interface AppBuilderInterface
    {
        public function useRouter(): RouteInterface;
    }

    class AppBuilder implements AppBuilderInterface
    {
        private RouteInterface $router;

        public function __construct()
        {
            $this->router = new Router();
        }
        public function useRouter(): RouteInterface
        {
            return $this->router;
        }
    }
}

namespace PPPk\Router {

    use Exception;

    interface RouteInterface
    {
        public function group(string $prefix, callable $callback): void;
        public function get(string $pattern, callable|array $handler, array $middlewares = []): void;
        public function post(string $pattern, callable|array $handler, array $middlewares = []): void;
        public function put(string $pattern, callable|array $handler, array $middlewares = []): void;
        public function delete(string $pattern, callable|array $handler, array $middlewares = []): void;
        public function dispatch(string $uri): void;
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
        public function get(string $pattern, callable|array $handler, array $middlewares = []): void
        {
            $this->addMethod(self::Get);
            $this->add($pattern, $handler, $middlewares);
        }

        public function post(string $pattern, callable|array $handler, array $middlewares = []): void
        {
            $this->addMethod(self::Post);
            $this->add($pattern, $handler, $middlewares);
        }

        public function put(string $pattern, callable|array $handler, array $middlewares = []): void
        {
            $this->addMethod(self::Put);
            $this->add($pattern, $handler, $middlewares);
        }

        public function delete(string $pattern, callable|array $handler, array $middlewares = []): void
        {
            $this->addMethod(self::Delete);
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
                        if (is_array($route["handler"])) {
                            $this->handlerController($route["handler"], $params);
                        }

                        if (is_object($route["handler"])) {
                            call_user_func_array($route["handler"], $params);
                        }
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

        private function add(string $pattern, callable|array $handler, array $middlewares = []): void
        {
            $regex = $this->convertToRegex($this->prefix . $pattern, $paramNames);

            $this->routes[] = [
                "regex" => $regex,
                "paramNames" => $paramNames,
                "handler" => $handler,
                "middlewares" => $middlewares,
            ];
        }

        private function addMethod(string $method): void
        {
            if ($_SERVER["REQUEST_METHOD"] !== $method) {
                throw new Exception("Invalid HTTP method. Expected {$method}", 405);
            }
        }

        private function handlerController(array $actionHandler, array $params)
        {
            list($controllerClass, $methodName) = $actionHandler;

            if (!class_exists($controllerClass)) {
                throw new Exception("Controller {$controllerClass} not found.");
            }

            $controller = new $controllerClass(); // constructor ...params
            if (!method_exists($controller, $methodName)) {
                throw new Exception("Method {$methodName} not found in controller {$controllerClass}.");
            }

            $params = count($params) ? $params : [];

            return call_user_func_array([$controller, $methodName], $params);
        }
    }
}

namespace PPPk\Helper {
}
