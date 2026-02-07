<?php

namespace Pano\Modules\Default\Commands;

use Pano\Core\BaseCommand;
use Pano\Enum\ResultCode;

class DefaultCommand extends BaseCommand
{

    public function handle(array $arguments): ResultCode
    {
        $this->info(env('APP_NAME', 'Pano'));
        return ResultCode::OK;
    }
}