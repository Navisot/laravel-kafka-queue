<?php

namespace Kafka\Contracts;

interface ConfContract
{
    public function set(string $key, string $value): void;
}