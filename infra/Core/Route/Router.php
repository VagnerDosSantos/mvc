<?php

namespace Infra\Core\Route;

class Router
{
    private array $routes = [];
    private string $requestUri;
    private string $requestMethod;
    private ?string $prefix = null;
    private ?string $middleware = null;

    public function __construct()
    {
        $this->requestUri = sanitize_uri($_SERVER['REQUEST_URI']);
        $this->requestMethod = $_SERVER['REQUEST_METHOD'];
    }

    public function list()
    {
        return $this->routes;
    }

    public function middleware(string $middleware, callable $callback)
    {
        $this->middleware = $middleware;

        $callback($this);

        $this->middleware = null;
    }

    public function prefix(string $prefix, callable $callback)
    {
        $this->prefix = sanitize_uri($prefix);

        $callback($this);

        $this->prefix = null;
    }

    public function get(string $uri, array $controller)
    {
        $uri = $this->createUri($uri);
        $uri = $this->replaceParams($uri);

        $this->routes['GET'][$uri] = $controller;
        $this->routes['GET'][$uri]['middleware'] = $this->middleware;
    }

    public function post(string $uri, array $controller)
    {
        $uri = $this->createUri($uri);
        $this->routes['POST'][$uri] = $controller;
        $this->routes['POST'][$uri]['middleware'] = $this->middleware;
    }

    public function put(string $uri, array $controller)
    {
        $uri = $this->createUri($uri);
        $uri = $this->replaceParams($uri);
        $this->routes['PUT'][$uri] = $controller;
        $this->routes['PUT'][$uri]['middleware'] = $this->middleware;
    }

    public function patch(string $uri, array $controller)
    {
        $uri = $this->createUri($uri);
        $uri = $this->replaceParams($uri);
        $this->routes['PATCH'][$uri] = $controller;
        $this->routes['PATCH'][$uri]['middleware'] = $this->middleware;
    }

    public function delete(string $uri, array $controller)
    {
        $uri = $this->createUri($uri);
        $uri = $this->replaceParams($uri);
        $this->routes['DELETE'][$uri] = $controller;
        $this->routes['DELETE'][$uri]['middleware'] = $this->middleware;
    }

    public function __destruct()
    {
        $this->dispatch();
    }

    private function replaceParams(?string $uri)
    {
        $requestUri = explode('/', $this->requestUri);
        $stringUri = explode('/', $uri);

        $uri = array_map(function ($value, $index) use ($requestUri) {
            $param = str_replace(['{', '}'], '', $value);

            if (preg_match('/\{[a-zA-Z0-9]+\}/', $value) && !empty($requestUri[$index])) {
                $_REQUEST[$param] = $requestUri[$index];
            }

            return preg_replace('/\{[a-zA-Z0-9]+\}/', $requestUri[$index] ?? $value, $value);
        }, $stringUri, array_keys($stringUri));

        return $uri = implode('/', $uri);
    }

    private function createUri(string $uri = null)
    {
        $uri = [$this->prefix, $uri];
        $uri = sanitize_uri(implode('/', $uri));
        $uri = trim($uri, '/');

        if (empty($uri)) {
            $uri = '/';
        }

        return $uri;
    }

    private function dispatch()
    {
        $controller = $this->routes[$this->requestMethod][$this->requestUri] ?? null;

        if (isset($this->routes[$this->requestMethod][$this->requestUri])) {
            $controller = $this->routes[$this->requestMethod][$this->requestUri];
        }

        if ($controller === null) {
            http_response_code(404);
            die('Página não encontrada');
        }

        $middleware = $controller['middleware'] ?? null;

        if ($middleware !== null) {
            $middleware::handle();
        }

        $method = $controller[1];
        $controller = $controller[0];

        $controller = new $controller();
        $controller->{$method}();
    }
}
