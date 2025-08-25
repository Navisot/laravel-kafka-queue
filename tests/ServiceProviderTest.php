<?php

use Orchestra\Testbench\TestCase;
use Kafka\KafkaServiceProvider;

class ServiceProviderTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [KafkaServiceProvider::class];
    }

    public function test_provider_registers_kafka_connector()
    {
        $manager = $this->app['queue'];
        $this->assertTrue(method_exists($manager, 'addConnector'));
        $this->assertNotNull($manager);
    }
}