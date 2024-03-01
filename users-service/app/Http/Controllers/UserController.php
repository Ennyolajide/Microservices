<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Services\AmqpService;
use PhpAmqpLib\Message\AMQPMessage;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'firstName' => 'required',
            'lastName' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::create($request->all());

        $this->publishUserCreatedEvent($user);

        return response()->json(['message' => 'User created'], 201);
    }

    protected function publishUserCreatedEvent($user)
    {
        $connection = AmqpService::getConnection();
        $channel = $connection->channel();

        $channel->queue_declare('user_events', false, true, false, false);

        $message = new AMQPMessage(json_encode($user->toArray()));
        $channel->basic_publish($message, '', 'user_events');

        $channel->close();
        $connection->close();
    }
}
