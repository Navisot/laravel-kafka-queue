<?php

namespace Kafka\Tests\Fakes;

use Kafka\Contracts\ConfContract;

class FakeConf implements ConfContract
{
    /**
     * @var array
     */
    public array $values = [];

    public function set(string $key, string $value): void
    {
        $this->values[$key] = $value;
    }
}