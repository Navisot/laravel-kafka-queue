<?php

namespace Kafka\Contracts;


interface KafkaFactoryContract
{
    public function makeConf(): ConfContract;
    public function makeProducer(ConfContract $conf): ProducerContract;
    public function makeConsumer(ConfContract $conf): ConsumerContract;
}