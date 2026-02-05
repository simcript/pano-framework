<?php


namespace Pano\Modules\Default\Handlers;

use Pano\Core\BaseHandler;
use Pano\Foundation\Response;

final class DefaultHandler extends BaseHandler
{

    public function info(): Response
    {
        return Response::html(
            $this->module->view()
            ->with(['name' => 'Pano'])
            ->layout('layout')
            ->render('home')
        );
    }
}