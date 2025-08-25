<?php

namespace Kafka\Infra;

use Kafka\Contracts\TopicContract;

final class ExtTopic implements TopicContract
{
    /**
     * @var \RdKafka\Topic
     */
    private \RdKafka\Topic $topic;

    public function __construct(\RdKafka\Topic $topic)
    {
        $this->topic = $topic;
    }

    public function produce(int $partition, int $msgflags, string $payload): void
    {
        $this->topic->produce($partition, $msgflags, $payload);
    }
}