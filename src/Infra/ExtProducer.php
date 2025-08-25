<?php

namespace Kafka\Infra;

use Kafka\Contracts\ProducerContract;
use Kafka\Contracts\TopicContract;

class ExtProducer implements ProducerContract
{
    /**
     * @var \RdKafka\Producer
     */
    private \RdKafka\Producer $producer;

    public function __construct(\RdKafka\Producer $producer)
    {
        $this->producer = $producer;
    }

    public function newTopic(string $name): TopicContract
    {
        return new ExtTopic($this->producer->newTopic($name));
    }

    public function flush(int $timeoutMs): int
    {
        return $this->producer->flush($timeoutMs);
    }
}