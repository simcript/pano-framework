<?php


namespace Pano\Modules\Default\Handlers;

use Pano\Core\BaseHandler;

final class DefaultHandler extends BaseHandler
{

    public function info(): void
    {
        $this->module->view()
            ->with(['name' => 'Pano'])
            ->layout('layout')
            ->render('home');
    }
}