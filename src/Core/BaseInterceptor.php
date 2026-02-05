<?php

namespace Pano\Core;

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
