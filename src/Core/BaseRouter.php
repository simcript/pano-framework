<?php

namespace Pano\Core;

use Pano\Enum\HttpMethod;

abstract class BaseRouter
{

    abstract public function get(string $path, callable $handler): void;

    abstract public function post(string $path, callable $handler): void;

    abstract public function put(string $path, callable $handler): void;

    abstract public function delete(string $path, callable $handler): void;

    abstract protected function notFound(): mixed;

    protected BaseRequest $request;

    private array $routes = [];
    private array $commands = [];

    public function __construct(BaseRequest $request)
    {
        $this->request = $request;
    }

    protected function register(HttpMethod $method, string $path, callable $handler): void
    {
        $path = trim($path, '/') . '/';

        [$pattern, $params] = $this->compile($path);

        $this->routes[$method->value][] = [
            'pattern' => $pattern,
            'params' => $params,
            'handler' => $handler,
        ];
    }

    public function command(string $path, string $commandClass): void
    {
        $this->commands[$path] = $commandClass;
    }

    public function dispatch(): mixed
    {
        $method = $this->request->getMethod();
        if ($method === HttpMethod::CLI) {
            $commandClass = $this->commands[$this->request->getUrl()] ?? null;
            if (!class_exists($commandClass)) {
                return $this->notFound();
            }
            $commandClass = (new $commandClass($this->request));
            if ($commandClass instanceof BaseCommand) {
                return $commandClass->handle();
            } else {
                return $this->notFound();
            }
        } else {
            $uri = $this->normalizeUri($this->request->getUrl());
            if (!isset($this->routes[$method->value])) {
                return $this->notFound();
            }
            foreach ($this->routes[$method->value] as $route) {
                if (preg_match($route['pattern'], $uri, $matches)) {
                    $args = [];
                    foreach ($route['params'] as $name) {
                        $args[] = $matches[$name];
                    }
                    return ($route['handler'])(...$args);
                }
            }
            return $this->notFound();
        }
    }

    protected function compile(string $path): array
    {
        $params = [];

        $path = $path === '/' ? '/' : rtrim($path, '/');

        $pattern = preg_replace_callback(
            '/\/?\[([a-zA-Z_][a-zA-Z0-9_]*)([\?\*])?\]/',
            function ($m) use (&$params) {
                $name = $m[1];
                $flag = $m[2] ?? null;

                $params[] = $name;

                // catch-all param [param*]
                if ($flag === '*') {
                    return '(?:/(?P<' . $name . '>.+))?';
                }

                // optional param [param?]
                if ($flag === '?') {
                    return '(?:/(?P<' . $name . '>[^/]+))?';
                }

                // required param [param]
                return '/(?P<' . $name . '>[^/]+)';
            },
            $path
        );

        return [
            '#^' . $pattern . '$#',
            $params
        ];
    }

    protected function normalizeUri(string $uri): string
    {
        return trim(parse_url($uri, PHP_URL_PATH) ?? '/', '/') ?: '/';
    }
}
