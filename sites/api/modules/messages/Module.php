<?php
namespace Modules;

use Fogito\Events\Manager as EventsManager;
use Fogito\Loader;
use Middlewares\Api;

class Module
{
    public function register($app)
    {
        $loader = new Loader();
        $loader->registerNamespaces([
            'Controllers' => __DIR__ . '/controllers',
        ]);
        $loader->register();

        $app->setDefaultNamespace('Controllers');

        $eventsManager = new EventsManager();
        $eventsManager->attach('dispatch', new Api);
        $app->setEventsManager($eventsManager);
    }
}
