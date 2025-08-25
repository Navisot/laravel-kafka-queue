<?php

namespace Kafka\Tests\Fakes;

use Kafka\Contracts\ProducerContract;
use Kafka\Contracts\TopicContract;

class FakeProducer implements ProducerContract
{
    /**
     * @var array
     */
    public array $produced = [];

    public function newTopic(string $name): TopicContract
    {
        return new FakeTopic($this, $name);
    }

    public function flush(int $timeoutMs): int
    {
        return 0;
    }
}