<?php

namespace Kafka\Tests\Fakes;

use Kafka\Contracts\ConsumerContract;

class FakeConsumer implements ConsumerContract
{
    /**
     * @var array
     */
    public array $subscriptions = [];

    /**
     * @var array
     */
    public array $messages = [];

    public function subscribe(array $topics): void
    {
        $this->subscriptions = $topics;
    }

    public function consume(int $timeoutMs): object
    {
        return array_shift($this->messages) ?? (object) ['err' => RD_KAFKA_RESP_ERR__TIMED_OUT];
    }

    public function pushMessage(object $m) : void
    {
        $this->messages[] = $m;
    }
}