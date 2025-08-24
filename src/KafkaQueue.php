<?php

namespace Kafka;

use Illuminate\Queue\Queue;
use Illuminate\Contracts\Queue\Queue as QueueContract;
use Exception;

/**
 * @method int pendingSize(string|null $queue = null)
 * @method int delayedSize(string|null $queue = null)
 * @method int reservedSize(string|null $queue = null)
 * @method null creationTimeOfOldestPendingJob(string|null $queue = null)
 */
class KafkaQueue extends Queue implements QueueContract
{
    private $producer, $consumer;

    public function __construct($producer, $consumer)
    {
        $this->producer = $producer;
        $this->consumer = $consumer;
    }

    public function size($queue = null){}

    public function push($job, $data = '', $queue = null)
    {
        $topic = $this->producer->newTopic($queue ?? env('KAFKA_QUEUE'));
        $topic->produce(RD_KAFKA_PARTITION_UA, 0, serialize($job));
        $this->producer->flush(1000);
    }

    public function pushRaw($payload, $queue = null, array $options = []){}

    public function later($delay, $job, $data = '', $queue = null){}

    public function pop($queue = null)
    {
        $this->consumer->subscribe([$queue ?? env('KAFKA_QUEUE')]);

        try {

            $message = $this->consumer->consume(120 * 1000);

            switch ($message->err) {
                case RD_KAFKA_RESP_ERR_NO_ERROR:
                    $job = unserialize($message->payload);
                    $job->handle();
                    break;
                case RD_KAFKA_RESP_ERR__PARTITION_EOF:
                    var_dump("No more messages");
                    break;
                case RD_KAFKA_RESP_ERR__TIMED_OUT:
                    var_dump("Timed out");
                    break;
                default:
                    throw new Exception($message->errstr(), $message->err);
            }
        } catch (Exception $e) {
            var_dump($e->getMessage());
        }
    }

    public function __call(string $name, array $arguments){}
}
