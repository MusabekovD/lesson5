<?php

namespace  App\Kernel\Router;

class Router
{

    private array $routes = [
        'GET' => [],
        'POST' => [],
    ];

    public function __construct()
    {
        $this->initRoutes();
    }

    public function dispatch(string $uri, string $method): void
    {
        $route = $this->findRoute($uri, $method);

        if (!$route) {
            $this->notFound();
        }

        if (is_array($route->getAction())) {
            [$controller, $action] = $route->getAction();

            $controller = new $controller;
            $controller->$action();

            call_user_func([$controller, $action]);
        } else {
            call_user_func($route->getAction()());
        }
    }
    private function notFound()
    {
        echo "404|Not Found";
        exit();
    }

    public function findRoute(string $uri, string $method): Route|false
    {
        if (!isset($this->routes[$method][$uri])) {
            return false;
        }
        return $this->routes[$method][$uri];
    }

    public function initRoutes()
    {
        $routes = $this->getRoutes();

        foreach ($routes as $route) {
            $this->routes[$route->getMethod()][$route->getUri()] = $route;
        }
    }
    public function getRoutes(): array
    {
        return require_once APP_PATH . '/config/routes.php';
    }
}
