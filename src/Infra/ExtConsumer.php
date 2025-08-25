<?php

namespace Kafka\Infra;

use Kafka\Contracts\ConsumerContract;

final class ExtConsumer implements ConsumerContract
{
    /**
     * @var \RdKafka\KafkaConsumer
     */
    private \RdKafka\KafkaConsumer $consumer;

    public function __construct(\RdKafka\KafkaConsumer $consumer )
    {
        $this->consumer = $consumer;
    }

    public function subscribe(array $topics): void
    {
        $this->consumer->subscribe($topics);
    }

    public function consume(int $timeoutMs): object
    {
        return $this->consumer->consume($timeoutMs);
    }
}