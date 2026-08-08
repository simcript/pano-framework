<?php

namespace Pano\Foundation;

use Pano\Kernel\BaseBoot;
use Pano\Kernel\BaseModule;
use Pano\Kernel\BaseRequest;
use Pano\Kernel\HttpMethodEnum;
use ReflectionClass;

final class Boot extends BaseBoot
{
    
    public function run(array $data, bool $cli = false): void
    {
        $requestClass = $cli ? CLIRequest::class : Request::class;
        $this->dispatcher($requestClass, $data);
    }

    protected function dispatcher($requestClass, ...$args): void
    {
        /** @var BaseRequest $request */
        $request = new $requestClass(...$args);
        try {
            $module = $request->getModule();
            $moduleName = config('modules.' . $module, null);
            if ($moduleName === null) {
                if (($module === '') || ($request->getMethod() === HttpMethodEnum::CLI)) {
                    throw new Exception("No module found for '$module'");
                }
                $args[] = '';
                $this->dispatcher($requestClass, ...$args);
                return;
            }
            if (!class_exists($moduleName)) {
                throw new Exception("Module class ($moduleName) not found");
            }
            $reflection = new ReflectionClass($moduleName);
            if (!$reflection->isSubclassOf(BaseModule::class)) {
                throw new Exception("Module ($moduleName) must extend " . BaseModule::class);
            }
            $reflection->newInstance($request)->routes()->handle();
        } catch (\Throwable $e) {
            Response::exception($e, $request)->send();
        }
    }

}
