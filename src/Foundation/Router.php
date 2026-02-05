<?php

namespace Pano\Foundation;

use Pano\Core\BaseRouter;
use Pano\Enum\HttpMethod;
use Pano\Enum\HttpStatus;

final class Router extends BaseRouter
{

    /**
     * @param string $path
     * @param string $class
     * @param string $action
     * @param array $interceptors
     * @throws Exception
     */
    public function get(string $path, string $class, string $action, array $interceptors = []): void
    {
        try {
            $this->register(HttpMethod::GET, $path, $class, $action, $interceptors);
        } catch (\Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode(), HttpStatus::INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @param string $path
     * @param string $class
     * @param string $action
     * @param array $interceptors
     * @throws Exception
     */
    public function post(string $path, string $class, string $action, array $interceptors = []): void
    {
        try {
            $this->register(HttpMethod::POST, $path, $class, $action, $interceptors);
        } catch (\Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode(), HttpStatus::INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @param string $path
     * @param string $class
     * @param string $action
     * @param array $interceptors
     * @throws Exception
     */
    public function put(string $path, string $class, string $action, array $interceptors = []): void
    {
        try {
            $this->register(HttpMethod::PUT, $path, $class, $action, $interceptors);
        } catch (\Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode(), HttpStatus::INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @param string $path
     * @param string $class
     * @param string $action
     * @param array $interceptors
     * @throws Exception
     */
    public function delete(string $path, string $class, string $action, array $interceptors = []): void
    {
        try {
            $this->register(HttpMethod::DELETE, $path, $class, $action, $interceptors);
        } catch (\Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode(), HttpStatus::INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @throws Exception
     */
    protected function notFound(): mixed
    {
        if (HttpMethod::CLI === $this->request->getMethod()) {
            throw new Exception('Command not found', 404, HttpStatus::NOT_FOUND);
        }

        throw new Exception('Route not found', 404, HttpStatus::NOT_FOUND);
    }

}