<?php

namespace Pano\Core;

use Exception;
use Pano\Enum\HttpMethod;
use ReflectionClass;
use ReflectionNamedType;

abstract class BaseRouter
{

    abstract public function get(string $path, string $class, string $action, array $interceptors = []): void;

    abstract public function post(string $path, string $class, string $action, array $interceptors = []): void;

    abstract public function put(string $path, string $class, string $action, array $interceptors = []): void;

    abstract public function delete(string $path, string $class, string $action, array $interceptors = []): void;

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

                    // instantiate interceptors
                    $interceptors = [];
                    foreach ($route['interceptors'] as $interceptorClass) {
                        $interceptors[] = new $interceptorClass($this->request);
                    }

                    // BEFORE
                    foreach ($interceptors as $interceptor) {
                        $interceptor->onRequest();
                        $this->request = $interceptor->request;
                    }

                    // HANDLER
                    $handler = new $route['handler'][0]($this->request, $this->module);

                    $response = $handler->{$route['handler'][1]}(...$args);
                    // AFTER (reverse order like Laravel)
                    foreach (array_reverse($interceptors) as $interceptor) {
                        $response = $interceptor->onResponse($response);
                    }

                    return $response->send();
                }
            }

            return $this->notFound();
        }
    }

    /**
     * @throws Exception
     */
    protected function register(
        HttpMethod $method,
        string     $path,
        string     $class,
        string     $action,
        array      $interceptors = []
    ): void
    {
        if (!class_exists($class)) {
            throw new Exception("Handler ($class) not found");
        }

        $this->checkHandler($class, $action);

        $path = trim($path, '/') . '/';

        [$pattern, $params] = $this->compile($path);
        foreach ($interceptors as $interceptor) {
            $reflection = new ReflectionClass($interceptor);
            if (!$reflection->isSubclassOf(BaseInterceptor::class)) {
                throw new Exception("Interceptor ($interceptor) must extend BaseInterceptor");
            }
        }
        $this->routes[$method->value][] = [
            'pattern' => $pattern,
            'params' => $params,
            'handler' => [$class, $action],
            'interceptors' => $interceptors,
        ];
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

    private function normalizeUri(string $uri): string
    {
        return trim(parse_url($uri, PHP_URL_PATH) ?? '/', '/') ?: '/';
    }

    private function checkHandler(string $class, string $action): void
    {
        $reflection = new ReflectionClass($class);

        if (!$reflection->isSubclassOf(BaseHandler::class)) {
            throw new Exception("Handler ($class) must extend " . BaseHandler::class);
        }

        if (!$reflection->hasMethod($action)) {
            throw new Exception("Action method $action is not defined in handler $class");
        }

        $method = $reflection->getMethod($action);

        if (!$method->isPublic()) {
            throw new Exception("Action method $action exists in handler $class but is not public");
        }

        $returnType = $method->getReturnType();

        if (!$returnType) {
            throw new Exception(
                "Action method $action in handler $class must declare a return type"
            );
        }

        if ($returnType instanceof ReflectionNamedType) {

            if ($returnType->isBuiltin()) {
                throw new Exception(
                    "Action method $action in handler $class must return BaseResponse"
                );
            }

            if ($returnType->getName() !== BaseResponse::class
                && !is_subclass_of($returnType->getName(), BaseResponse::class)
            ) {
                throw new Exception(
                    "Action method $action in handler $class must return BaseResponse"
                );
            }
        }

    }
}
