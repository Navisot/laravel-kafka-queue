<?php

namespace Kafka\Tests\Fakes;

use Kafka\Contracts\TopicContract;

class FakeTopic implements TopicContract
{
    private FakeProducer $p;
    private string $name;

    public function __construct(FakeProducer $p, string $name)
    {
        $this->p = $p;
        $this->name = $name;
    }

    public function produce(int $partition, int $msgflags, string $payload): void
    {
        $this->p->produced[] = ['topic' => $this->name, 'payload' => $payload];
    }
}