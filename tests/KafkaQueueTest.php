<?php

use PHPUnit\Framework\TestCase;
use Kafka\Tests\Fakes\FakeFactory;
use Kafka\KafkaConnector;
use Kafka\Tests\Fakes\DummyJob;
Use Kafka\Tests\Fakes\HandleHelper;
use Kafka\Tests\Fakes\HandlingJob;

class KafkaQueueTest extends TestCase
{
    public function test_push_produces_message()
    {
        $factory = new FakeFactory();

        $connector = new KafkaConnector($factory);
        $queue = $connector->connect([
            'bootstrap_servers' => 'broker:9092',
            'security_protocol' => 'test-ssl',
            'sasl_mechanisms' => 'test-mechanism',
            'sasl_username' => 'username',
            'sasl_password' => 'password',
            'group_id' => 'test-group',
        ]);

        $queue->push(new DummyJob(), '', 'topic-a');
        $this->assertCount(1, $factory->producer->produced);
        $this->assertSame('topic-a', $factory->producer->produced[0]['topic']);
        $this->assertNotEmpty($factory->producer->produced[0]['payload']);
    }

    public function test_pop_handles_message_payload()
    {
        $factory = new FakeFactory();
        $connector = new \Kafka\KafkaConnector($factory);
        $queue = $connector->connect([
            'bootstrap_servers' => 'broker:9092',
            'security_protocol' => 'test-ssl',
            'sasl_mechanisms' => 'test-mechanism',
            'sasl_username' => 'username',
            'sasl_password' => 'password',
            'group_id' => 'test-group',
        ]);

        HandleHelper::$handled = false;

        $factory->consumer->pushMessage((object)[
            'err' => RD_KAFKA_RESP_ERR_NO_ERROR,
            'payload' => serialize(new HandlingJob()),
        ]);

        $queue->pop('topic-a');

        $this->assertTrue(HandleHelper::$handled, 'HandlingJob::handle() should have been called');
    }
}