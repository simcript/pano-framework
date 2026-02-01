<?php


namespace Pano\Modules\Default;

use Pano\Core\BaseLogger;
use Pano\Core\BaseModule;
use Pano\Core\BaseRouter;
use Pano\Core\BaseView;
use Pano\Foundation\Logger;
use Pano\Foundation\Router;
use Pano\Foundation\View;
use Pano\Modules\Default\Commands\DefaultCommand;

final readonly class DefaultModule extends BaseModule
{
    public function routes(): BaseRouter
    {
        $router = new Router($this->request);
        $router->get('/', fn() => $this->info());

        return $router;
    }

    public function commands(): array
    {
        return [
            'app:info' => DefaultCommand::class
        ];
    }

    protected function view(): BaseView
    {
        return new View($this->viewPath());
    }

    protected function log(): BaseLogger
    {
        return new Logger($this->logPath());
    }

    protected function info(): void
    {
        $this->view()
            ->with(['name' => 'Pano'])
            ->layout('layout')
            ->render('home');
    }
}