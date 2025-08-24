<?php

namespace Kafka;

use Illuminate\Queue\Queue;
use Illuminate\Contracts\Queue\Queue as QueueContract;
use Exception;
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
    /**
     * @var Producer
     */
    private $producer;

    /**
     * @var KafkaConsumer
     */
    private $consumer;

    public function __construct($producer, $consumer)
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
        $payload = serialize($job);

        $topic->produce(RD_KAFKA_PARTITION_UA, 0, $payload);

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

        try {

            $message = $this->consumer->consume(120 * 1000);

            switch ($message->err) {
                case RD_KAFKA_RESP_ERR_NO_ERROR:
                    $job = unserialize($message->payload);
                    $job->handle();
                    break;

                case RD_KAFKA_RESP_ERR__PARTITION_EOF:
                    break;

                case RD_KAFKA_RESP_ERR__TIMED_OUT:
                    break;

                default:
                    throw new Exception($message->errstr(), $message->err);
            }
        } catch (Exception $e) {
            logger()->error('Kafka consumer error: '. $e->getMessage(), ['exception' => $e]);
        }
    }

    public function __call(string $name, array $arguments){}
}
