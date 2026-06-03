<?php

namespace Pano\Kernel;

abstract class BaseInterceptor
{

    public function __construct(public readonly BaseRequest $request)
    {
    }

    public function onRequest(): void
    {
    }

    public function onResponse(BaseResponse $response): BaseResponse
    {
        return $response;
    }

}
