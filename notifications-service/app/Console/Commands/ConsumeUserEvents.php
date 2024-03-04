<?php

namespace App\Console\Commands;

use Exception;
use App\Events\UserCreated;
use Illuminate\Console\Command;
use App\Services\AmqpService;
use PhpAmqpLib\Exception\AMQPTimeoutException;
use Symfony\Component\Console\Output\OutputInterface;


class ConsumeUserEvents extends Command
{
    public $messageData;

    protected $signature = 'user-events:consume';

    protected $description = 'Consume user events from RabbitMQ';


    public function handle()
    {
        try {
            $this->listenForUserEvents($this->output);
        } catch (AMQPTimeoutException $e) {
            // Handle timeout exceptions (e.g., if RabbitMQ server is not reachable)
            $this->error('Failed to connect to RabbitMQ server: ' . $e->getMessage());
        } catch (Exception $e) {
            // Handle other generic exceptions
            $this->error('An error occurred: ' . $e->getMessage());
        }
    }

    protected function listenForUserEvents(OutputInterface $output)
    {
        $connection = AmqpService::getConnection();
        $channel = $connection->channel();

        $channel->queue_declare('user_events', false, true, false, false);

        $output->writeln(" [*] Waiting for user events. To exit press CTRL+C\n");

        $callback = function ($msg) use ($output) {
            $this->messageData = json_decode($msg->body, true);
            $output->writeln(' [x] User created: ' . json_encode($this->messageData) . "\n");
            $this->handleUserEvent($output);
            $msg->ack();
        };

        $channel->basic_consume('user_events', '', false, false, false, false, $callback);

        while ($channel->is_consuming()) {
            $channel->wait();
        }

        $channel->close();
        $connection->close();
    }

    public function handleUserEvent(OutputInterface $output)
    {
        $output->writeln(' [x] User created: ' . json_encode($this->messageData));

        // Dispatch UserCreated event
        event(new UserCreated($this->messageData));

        // Acknowledge the message to RabbitMQ that it has been successfully processed
        $output->writeln(' [x] Acknowledging message...');
    }
}
