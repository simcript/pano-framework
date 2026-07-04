<?php

namespace Pano\Foundation;

use Pano\Kernel\BaseBoot;
use Pano\Kernel\BaseModule;
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
        /** @var Request $request */
        $this->request = new Request($_SERVER);
        $this->dispatcher();
    }

    public function cli(array $arguments): void
    {
        /** @var CLIRequest $request */
        $this->request = new CLIRequest($arguments);
        $this->dispatcher();
    }

    private function dispatcher(): void
    {
        try {
            $module = $this->request->getModule();

            $moduleName = config('modules.' . $module, null);
            if ($moduleName === null) {
                if ($module === '') {
                    throw new Exception("No module found for '$module'");
                }
                $this->request->setModule($module);
                $this->dispatcher();
                return;
            }
            if (!class_exists($moduleName)) {
                throw new Exception("Module class ($moduleName) not found");
            }
            $reflection = new ReflectionClass($moduleName);
            if (!$reflection->isSubclassOf(BaseModule::class)) {
                throw new Exception("Module ($moduleName) must extend " . BaseModule::class);
            }
            $reflection->newInstance($this->request)->routes()->handle();
        } catch (\Throwable $e) {
            Response::exception($e, $this->request)->send();
        }
    }

}
