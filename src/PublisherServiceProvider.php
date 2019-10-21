<?php

namespace EvolutionCMS\ZeeyN;

use EvolutionCMS\ServiceProvider;

class PublisherServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->loadPluginsFrom(
            dirname(__DIR__) . '/plugins/'
        );
    }
}
