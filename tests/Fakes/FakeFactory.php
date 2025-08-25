<?php

namespace Kafka\Tests\Fakes;

use Kafka\Contracts\ConfContract;
use Kafka\Contracts\ConsumerContract;
use Kafka\Contracts\KafkaFactoryContract;
use Kafka\Contracts\ProducerContract;

class FakeFactory implements KafkaFactoryContract
{
    /**
     * @var FakeProducer
     */
    public FakeProducer $producer;

    /**
     * @var FakeConsumer
     */
    public FakeConsumer $consumer;

    public function __construct()
    {
        $this->producer = new FakeProducer();
        $this->consumer = new FakeConsumer();
    }

    public function makeConf(): ConfContract
    {
        return new FakeConf();
    }

    public function makeProducer(ConfContract $conf): ProducerContract
    {
        return $this->producer;
    }

    public function makeConsumer(ConfContract $conf): ConsumerContract
    {
        return $this->consumer;
    }
}