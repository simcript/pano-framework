<?php

namespace Pano\Modules\Default\Commands;

use Composer\InstalledVersions;
use Pano\Core\BaseCommand;
use Pano\Core\ResultCodeEnum;

class DefaultCommand extends BaseCommand
{

    public function handle(array $arguments): ResultCodeEnum
    {
        $version = InstalledVersions::getPrettyVersion('simcript/pano') ?? 'dev';
        $this->info(env('APP_NAME', 'Pano') . " - " . $version);
        return ResultCodeEnum::OK;
    }
}