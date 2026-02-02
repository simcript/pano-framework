<?php

namespace Pano\Core;

use Pano\Foundation\Router;

abstract readonly class BaseModule
{
    abstract protected function routes(): BaseRouter;

    abstract public function view(): BaseView;

    abstract public function log(): BaseLogger;

    protected BaseRouter $router;

    public function __construct(
        protected BaseRequest $request
    )
    {
        $this->router = new Router($this->request, $this);
    }

    protected function viewPath(): string
    {
        return $this->path('Views');
    }

    protected function filePath(): string
    {
        return $this->path('Files');
    }

    protected function logPath(): string
    {
        return $this->path('Logs');
    }

    public function path(string $path = ''): string
    {
        $reflector = new \ReflectionClass(static::class);

        return dirname($reflector->getFileName()) . DIRECTORY_SEPARATOR . $path;
    }
}
