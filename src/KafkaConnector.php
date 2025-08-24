<?php

namespace Kafka;

use Illuminate\Queue\Connectors\ConnectorInterface;
use RdKafka\Conf;
use RdKafka\KafkaConsumer;
use RdKafka\Producer;
use RuntimeException;
use InvalidArgumentException;

class KafkaConnector implements ConnectorInterface
{

    /**
     * @param array $config
     * @return KafkaQueue
     */
    public function connect(array $config)
    {
        if (!extension_loaded('rdkafka')) {
            throw new RuntimeException(
                'Kafka driver requires the "rdkafka" PHP extension.'
            );
        }

        if (!class_exists(Conf::class)) {
            throw new RuntimeException(
                'ext-rdkafka appears missing or not enabled (RdKafka\\Conf not found).'
            );
        }

        foreach (['bootstrap_servers', 'group_id'] as $required) {
            if (empty($config[$required])) {
                throw new InvalidArgumentException("Missing Kafka config: {$required}");
            }
        }

        $conf = new Conf();

        $conf->set('bootstrap.servers', $config['bootstrap_servers']);

        if (!empty($config['security_protocol'])) {
            $conf->set('security.protocol', $config['security_protocol']);
        }

        if (!empty($config['sasl_mechanisms'])) {
            $conf->set('sasl.mechanisms', $config['sasl_mechanisms']);
        }

        if (!empty($config['sasl_username'])) {
            $conf->set('sasl.username', $config['sasl_username']);
        }

        if (!empty($config['sasl_password'])) {
            $conf->set('sasl.password', $config['sasl_password']);
        }

        $producer = new Producer($conf);

        $conf->set('group.id', $config['group_id']);

        $conf->set('auto.offset.reset', $config['auto_offset_reset']);

        $consumer = new KafkaConsumer($conf);

        return new KafkaQueue($producer, $consumer);
    }
}
