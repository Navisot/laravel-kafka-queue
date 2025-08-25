<?php

namespace Kafka\Infra;

use Kafka\Contracts\ConfContract;

final class ExtConf implements ConfContract
{
    /**
     * @var \RdKafka\Conf
     */
    public \RdKafka\Conf $inner;

    public function __construct() {
        $this->inner = new \RdKafka\Conf();
    }

    public function set(string $key, string $value): void
    {
        $this->inner->set($key, $value);
    }
}