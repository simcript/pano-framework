<?php

namespace Pano\Core;

use Exception;
use Pano\Enum\HttpMethod;
use ReflectionClass;

abstract class BaseRouter
{

    abstract public function get(string $path, string $class, string $action): void;

    abstract public function post(string $path, string $class, string $action): void;

    abstract public function put(string $path, string $class, string $action): void;

    abstract public function delete(string $path, string $class, string $action): void;

    abstract protected function notFound(): mixed;

    protected BaseRequest $request;
    protected BaseModule $module;

    private array $routes = [];
    private array $commands = [];

    public function __construct(BaseRequest $request, BaseModule $module)
    {
        $this->request = $request;
        $this->module = $module;
    }

    /**
     * @throws Exception
     */
    protected function register(HttpMethod $method, string $path, string $class, string $action): void
    {

        if (!class_exists($class)) {
            throw new Exception("Handler ($class) not found");
        }

        $reflection = new ReflectionClass($class);

        if (!$reflection->isSubclassOf(BaseHandler::class)) {
            throw new Exception("Handler ($class) must extend " . BaseHandler::class);
        }

        if (!$reflection->hasMethod($action)) {
            throw new Exception("Action method $action is not defined in handler $class");
        }

        if (!$reflection->getMethod($action)->isPublic()) {
            throw new Exception("Action method $action exists in handler $class but is not public");
        }

        $path = trim($path, '/') . '/';

        [$pattern, $params] = $this->compile($path);

        $this->routes[$method->value][] = [
            'pattern' => $pattern,
            'params' => $params,
            'handler' => [$class, $action],
        ];
    }

    public function command(string $path, string $class): void
    {
        $this->commands[$path] = $class;
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

                    return (new $route['handler'][0]($this->request, $this->module))->{$route['handler'][1]}(...$args);
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
