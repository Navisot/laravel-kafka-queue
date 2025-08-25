<?php

namespace Kafka\Infra;

use Kafka\Contracts\ConfContract;
use Kafka\Contracts\ConsumerContract;
use Kafka\Contracts\KafkaFactoryContract;
use Kafka\Contracts\ProducerContract;

final class ExtKafkaFactoryContract implements KafkaFactoryContract
{
    public function makeConf(): ConfContract
    {
        return new ExtConf();
    }

    public function makeProducer(ConfContract $conf): ProducerContract
    {
        assert($conf instanceof ExtConf, 'ExtKafkaFactory expects ExtConf');
        return new ExtProducer(new \RdKafka\Producer($conf->inner));
    }

    public function makeConsumer(ConfContract $conf): ConsumerContract
    {
        assert($conf instanceof ExtConf, 'ExtKafkaFactory expects ExtConf');
        return new ExtConsumer(new \RdKafka\KafkaConsumer($conf->inner));
    }
}