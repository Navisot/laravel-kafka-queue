<?php

namespace Kafka\Contracts;

interface ConsumerContract
{
    public function subscribe(array $topics): void;
    public function consume(int $timeoutMs): object;
}