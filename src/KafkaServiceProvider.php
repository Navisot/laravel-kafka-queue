<?php

namespace Kafka;

use Illuminate\Support\ServiceProvider;

class KafkaServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     */
    public function boot()
    {
        $manager = $this->app['queue'];

        $manager->addConnector('kafka', function() {
            return new KafkaConnector;
        });
    }
}
