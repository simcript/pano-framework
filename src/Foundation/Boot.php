<?php

namespace Pano\Foundation;

use Pano\Core\BaseBoot;
use Pano\Core\BaseModule;
use ReflectionClass;

final class Boot extends BaseBoot
{

    public function __construct()
    {
        $this->envLoader();
        $this->debug(config('app.debug', false));
    }

    public function run(): void
    {
        $this->request = new Request($_SERVER);
        try {
            $moduleClass = $this->getModule($this->request->getModule());
            $module = $moduleClass->newInstance($this->request);
            $module->routes()->dispatch();
        } catch (\Throwable $e) {
            Response::exception($e, $this->request)->send();
        }
    }

    public function cli(array $arguments): void
    {
        $this->request = new CLIRequest($arguments);
        try {
            $moduleClass = $this->getModule($this->request->getModule());
            $module = $moduleClass->newInstance($this->request);
            $module->command()->handle();
        } catch (\Throwable $e) {
            Response::exception($e, $this->request)->send();
        }
    }

    /**
     * @throws Exception
     */
    private function getModule(string $module): ReflectionClass
    {
        $moduleName = config('modules.' . $module, null);
        if ($moduleName === null) {
            throw new Exception("Module ({$module}) is not defined");
        }
        if (!class_exists($moduleName)) {
            throw new Exception("Module class ($moduleName) not found");
        }
        $reflection = new ReflectionClass($moduleName);
        if (!$reflection->isSubclassOf(BaseModule::class)) {
            throw new Exception("Module ($moduleName) must extend " . BaseModule::class);
        }

        return $reflection;
    }
}
