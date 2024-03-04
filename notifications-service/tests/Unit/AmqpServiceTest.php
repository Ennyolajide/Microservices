<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\AmqpService;
use PhpAmqpLib\Connection\AMQPStreamConnection;

class AmqpServiceTest extends TestCase
{
    public function testGetConnection()
    {
        $connection = AmqpService::getConnection();
        $this->assertInstanceOf(AMQPStreamConnection::class, $connection);
    }
}
