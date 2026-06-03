<?php

namespace Pano\Core;

abstract class BaseCommand
{

    public function __construct(
        public readonly BaseRequest $request,
        public readonly BaseModule  $module
    ){
    }

    abstract public function handle(array $arguments): ResultCodeEnum;

    protected function info(string $text): void
    {
        echo "\033[32m$text\033[0m\n";
    }

    protected function error(string $text): void
    {
        echo "\033[31m$text\033[0m\n";
    }
}