<?php

namespace Pano\Modules\Default\Interceptors;

use Pano\Core\BaseInterceptor;
use Pano\Core\BaseResponse;

class DefaultInterceptor extends BaseInterceptor
{
    public function onRequest(): void
    {
    }

    public function onResponse(BaseResponse $response): BaseResponse
    {
        return parent::onResponse($response);
    }

}