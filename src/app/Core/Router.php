<?php

namespace App\Core;

class Router
{
    protected array $staticRoutes = [];
    protected array $dynamicRoutes = [];
    protected string $prefix = '';

    public function add(string $pattern, callable $handler): void
    {
        if (str_starts_with($pattern, '#')) {
            $this->dynamicRoutes[] = [$this->buildPattern($pattern), $handler];
        } else {
            $fullPath = $this->prefix . $pattern;
            $this->staticRoutes[$fullPath] = $handler;
        }
    }

    public function post(string $pattern, callable $handler): void
    {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $this->add($pattern, $handler);
        }
    }

    public function group(string $prefix, callable $callback): void
    {
        $previousPrefix = $this->prefix;
        $this->prefix .= $prefix;
        $callback($this);
        $this->prefix = $previousPrefix;
    }

    public function dispatch(string $uri): void
    {
        if (isset($this->staticRoutes[$uri])) {
            call_user_func($this->staticRoutes[$uri]);
            return;
        }

        foreach ($this->dynamicRoutes as [$pattern, $handler]) {
            if (preg_match($pattern, $uri, $matches)) {
                array_shift($matches);
                call_user_func_array($handler, $matches);
                return;
            }
        }

        http_response_code(404);
        echo "404 Not Found";
    }

    protected function buildPattern(string $pattern): string
    {
        $cleanPattern = trim($pattern, '#');
        $fullPattern = $this->prefix . $cleanPattern;
        return "#^" . $fullPattern . "$#";
    }
}
