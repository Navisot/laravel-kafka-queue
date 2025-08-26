<?php

namespace Kafka\Tests;

use PHPUnit\Framework\TestCase;
use Kafka\KafkaConnector;
use Kafka\Tests\Fakes\FakeFactory;
use InvalidArgumentException;

class KafkaConnectorTest extends TestCase
{
    public function test_connect_builds_queue_with_producer_and_consumer()
    {
        $connector = new KafkaConnector(new FakeFactory());

        $queue = $connector->connect([
            'bootstrap_servers' => 'broker:9092',
            'security_protocol' => 'test-ssl',
            'sasl_mechanisms' => 'test-mechanism',
            'sasl_username' => 'username',
            'sasl_password' => 'password',
            'group_id' => 'test-group',
        ]);

        $this->assertInstanceOf(\Kafka\KafkaQueue::class, $queue);
    }

    public function test_missing_required_config_throws_exception()
    {
        $this->expectException(InvalidArgumentException::class);
        $connector = new KafkaConnector(new FakeFactory());
        $connector->connect(['bootstrap_servers' => 'broker_9092']);
    }
}