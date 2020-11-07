<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\User;
use Illuminate\Http\Request;
use Response;

class MessageController extends Controller
{
    public function getUsersListForMessages(Request $request)
    {
        $loggedInUser = User::where('remember_token', $request->token)->first();

        $receivers = Message::where('senderId', $loggedInUser->id)->get()->pluck('receiverId')->toArray();

        $senders = Message::where('receiverId', $loggedInUser->id)->get()->pluck('senderId')->toArray();

        $usersIdList = array_merge($receivers, $senders);

        $usersIdList = array_unique($usersIdList);

        $result = [];
        foreach ($usersIdList as $userId) {
            $user = User::where('id', $userId)->first();

            $message1 = Message::where('senderId', $user->id)->where('receiverId', $loggedInUser->id)->orderBy('id', 'desc')->first();

            $message2 = Message::where('receiverId', $user->id)->where('senderId', $loggedInUser->id)->orderBy('id', 'desc')->first();

            if (isset($message1) && isset($message2)) {
                if ($message1->created_at > $message2->created_at) {
                    $message = $message1->message;
                } else if ($message1->created_at < $message2->created_at) {
                    $message = $message2->message;
                }
            } else if (isset($message1)) {
                $message = $message1->message;
            } else if (isset($message2)) {
                $message = $message2->message;
            }
            $result[] = [
                'id' => $user->id,
                'name' => $user->first_name . ' ' . $user->last_name,
                'image' => $user->image,
                'message' => $message
            ];
        }

        return Response::json($result, 200);
    }

    public function getMessagesByUser(Request $request)
    {
        $loggedInUser = User::where('remember_token', $request->token)->first();

        $loggedInUserMessages = Message::where('senderId', $loggedInUser->id)->where('receiverId', $request->userId)->get()->toArray();

        $otherUserMessages = Message::where('senderId', $request->userId)->where('receiverId', $loggedInUser->id)->get()->toArray();

        $messagesList = array_merge($loggedInUserMessages, $otherUserMessages);

        sort($messagesList);

        return Response::json($messagesList);
    }

    public function getLoggedInUserId(Request $request)
    {
        $loggedInUser = User::where('remember_token', $request->token)->first();

        return Response::json($loggedInUser->id);
    }

    public function addMessage(Request $request)
    {
        $loggedInUser = User::where('remember_token', $request->token)->first();

        $message = new Message;

        $message->senderId = $loggedInUser->id;
        $message->receiverId = $request->userId;
        $message->message = $request->message;
        $message->isRead = false;

        $message->save();

        return Response::json($message);
    }

    public function getUnReadCount(Request $request)
    {
        $loggedInUser = User::where('remember_token', $request->token)->first();

        $count = Message::where('receiverId', $loggedInUser->id)->where('isRead', false)->count();

        return Response::json($count);
    }

    public function setMessagesRead(Request $request)
    {
        $count = 0;
        $messages = Message::where('senderId', $request->userId)->where('isRead', false)->get();

        foreach ($messages as $message) {
            $message->isRead = true;
            $message->save();

            $count++;
        }

        return Response::json($count);
    }
}
