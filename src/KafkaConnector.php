<?php

namespace Kafka;

use Illuminate\Queue\Connectors\ConnectorInterface;
use Kafka\Contracts\KafkaFactoryContract;
use Kafka\Infra\ExtKafkaFactoryContract;
use InvalidArgumentException;

class KafkaConnector implements ConnectorInterface
{

    public array $required = [
        'bootstrap_servers',
        'security_protocol',
        'sasl_mechanism',
        'sasl_username',
        'sasl_password',
        'group_id',
    ];

    public function __construct(private ?KafkaFactoryContract $factory = null)
    {
        $this->factory ??= new ExtKafkaFactoryContract();
    }

    /**
     * @param array $config
     * @return KafkaQueue
     */
    public function connect(array $config): KafkaQueue
    {
        if (!extension_loaded('rdkafka') || !class_exists(\RdKafka\Conf::class)) {
            throw new \RuntimeException(
                'Kafka driver requires the "rdkafka" PHP extension.'
            );
        }

        foreach ($this->required as $r) {
            if (empty($config[$r])) {
                throw new InvalidArgumentException("Missing Kafka config: {$r}");
            }
        }

        $conf = $this->factory->makeConf();
        // Required
        $conf->set('bootstrap.servers', $config['bootstrap_servers']);
        $conf->set('security.protocol', $config['security_protocol']);
        $conf->set('sasl.mechanism', $config['sasl_mechanism']);
        $conf->set('sasl.username', $config['sasl_username']);
        $conf->set('sasl.password', $config['sasl_password']);
        $conf->set('group.id', $config['group_id']);
        // Optional
        if (!empty($config['auto_offset_reset']))    $conf->set('auto.offset.reset',    $config['auto_offset_reset']);

        $producer = $this->factory->makeProducer($conf);
        $consumer = $this->factory->makeConsumer($conf);
        return new KafkaQueue($producer, $consumer);
    }
}
