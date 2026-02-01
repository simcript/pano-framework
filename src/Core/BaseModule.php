<?php

namespace Pano\Core;

abstract readonly class BaseModule
{
    abstract protected function routes(): BaseRouter;

    abstract protected function view(): BaseView;

    abstract protected function log(): BaseLogger;

    abstract public function commands(): array;

    public function __construct(
        protected BaseRequest $request
    )
    {
    }

    public function command(): BaseCommand|null
    {
        $commandClass = $this->commands()[$this->request->getUrl()] ?? null;
        return $commandClass ? new $commandClass($this->request) : null;
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
