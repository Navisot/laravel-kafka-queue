<?php

namespace Kafka\Contracts;

interface ProducerContract
{
    public function newTopic(string $name): TopicContract;
    public function flush(int $timeoutMs): int;
}