<?php

namespace Kafka\Contracts;

interface TopicContract
{
    public function produce(int $partition, int $msgflags, string $payload): void;
}