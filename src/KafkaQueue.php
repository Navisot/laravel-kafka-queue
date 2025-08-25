<?php

namespace Kafka;

use RuntimeException;
use Illuminate\Queue\Queue;
use Illuminate\Contracts\Queue\Queue as QueueContract;
use Kafka\Contracts\ConsumerContract;
use Kafka\Contracts\ProducerContract;
use RdKafka\Producer;
use RdKafka\KafkaConsumer;
use InvalidArgumentException;

/**
 * @method int pendingSize(string|null $queue = null)
 * @method int delayedSize(string|null $queue = null)
 * @method int reservedSize(string|null $queue = null)
 * @method null creationTimeOfOldestPendingJob(string|null $queue = null)
 */
class KafkaQueue extends Queue implements QueueContract
{
    private ProducerContract $producer;
    private ConsumerContract $consumer;

    public function __construct(ProducerContract $producer, ConsumerContract $consumer)
    {
        $this->producer = $producer;
        $this->consumer = $consumer;
    }

    public function size($queue = null){}

    public function push($job, $data = '', $queue = null)
    {
        $topicName = $queue ?? env('KAFKA_QUEUE');
        if (!$topicName) {
            throw new InvalidArgumentException('Kafka topic not provided (queue name / KAFKA_QUEUE).');
        }

        $topic = $this->producer->newTopic($topicName);
        $topic->produce(RD_KAFKA_PARTITION_UA, 0, serialize($job));
        $this->producer->flush(1000);
    }

    public function pushRaw($payload, $queue = null, array $options = []){}

    public function later($delay, $job, $data = '', $queue = null){}

    public function pop($queue = null)
    {
        $topicName = $queue ?? env('KAFKA_QUEUE');
        if (!$topicName) {
            throw new InvalidArgumentException('Kafka topic not provided (queue name / KAFKA_QUEUE).');
        }

        $this->consumer->subscribe([$topicName]);

        $message = $this->consumer->consume(120 * 1000);

        switch ($message->err) {
            case RD_KAFKA_RESP_ERR_NO_ERROR:
                $job = unserialize($message->payload);
                $job->handle();
                break;
            case RD_KAFKA_RESP_ERR__PARTITION_EOF:
            case RD_KAFKA_RESP_ERR__TIMED_OUT:
                break;
            default:
                $errstr = method_exists($message, 'errstr') ? $message->errstr() : 'Kafka error';
                throw new RuntimeException($errstr, $message->err);
        }
    }

    public function __call(string $name, array $arguments){}
}
