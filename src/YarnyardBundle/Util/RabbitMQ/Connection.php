<?php

namespace YarnyardBundle\Util\RabbitMQ;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class Connection
{
    /**
     * @var AMQPStreamConnection
     */
    protected $connection;

    /**
     * @param string $host
     * @param int    $port
     * @param string $name
     * @param string $pass
     */
    public function __construct(
        string $host,
        int $port,
        string $name,
        string $pass
    ) {
        $this->connection = new AMQPStreamConnection($host, $port, $name, $pass);
    }

    /**
     * @param string $key
     * @param string $body
     */
    public function publish(string $key, string $body)
    {
        $channel = $this->connection->channel();
        $msg = new AMQPMessage($body);
        $channel->basic_publish($msg, '', $key);
        $channel->close();
    }
}
