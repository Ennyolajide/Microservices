<?php

namespace App\Services;

use PhpAmqpLib\Connection\AMQPStreamConnection;

class AmqpService
{
    public static function getConnection()
    {
        $host = env('RABBITMQ_HOST', 'localhost');
        $port = env('RABBITMQ_PORT', 5672);
        $user = env('RABBITMQ_USER', 'guest');
        $password = env('RABBITMQ_PASSWORD', 'guest');

        return new AMQPStreamConnection($host, $port, $user, $password);
    }
}
