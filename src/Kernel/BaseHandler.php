<?php

namespace Pano\Kernel;

abstract class BaseHandler
{
    public function __construct(
        public readonly BaseRequest $request,
        public readonly BaseModule  $module
    )
    {
    }

}