<?php

namespace App\Http\Controllers;

use App\Models\Bid;
use App\Models\Message;
use App\Models\Post;
use App\User;
use Illuminate\Http\Request;
use Response;

class BidController extends Controller
{
    public function getBids(Request $request)
    {
        $bids = Bid::with('user:id,first_name,last_name,remember_token')->where('postId', $request->postId)->orderBy('id', 'desc')->take(3)->get();

        return Response::json($bids);
    }

    public function addBid(Request $request)
    {
        $user = User::where('remember_token', $request->token)->first();
        $post = Post::where('id', $request->postId)->first();

        if ($post->isActive) {
            if (!($post->user_id === $user->id)) {
                $lastBid = Bid::where('postId', $request->postId)->orderBy('id', 'desc')->first();

                if (!isset($lastBid->userId) || !($lastBid->userId == $user->id)) {
                    $bid = new Bid;

                    $bid->userId = $user->id;
                    $bid->postId = $request->postId;
                    $bid->price = $request->price;
                    $bid->message = $request->message;

                    $bid->save();

                    $post = Post::with('user:id,first_name,last_name,remember_token')->with('bids')->where('id', $request->postId)->first();

                    return Response::json($post);
                }

                return Response::json('Your bid is already highest', 400);
            }

            return Response::json('You can\'t bid on your own post', 400);
        }

        return Response::json('This post has been sold', 400);
    }

    public function bidAccepeted(Request $request)
    {

        $bid = Bid::where('id', $request->bidId)->first();

        $post = Post::where('id', $bid->postId)->first();
        $post->isActive = false;
        $post->soldTo = $bid->userId;
        $post->save();

        $user = User::where('id', $post->user_id)->first();

        $message = new Message;

        $message->senderId = $post->user_id;
        $message->receiverId = $bid->userId;
        $message->message = 'Congratulations!! ' . $user->first_name . ' ' . $user->last_name . ' has accepted your bid. Now you can send messages to talk in detail.';
        $message->isRead = false;
        $message->link = "/post/$post->id";

        $message->save();

        $post = Post::with('user:id,first_name,last_name,remember_token')->with('bids')->where('id', $bid->postId)->first();

        return Response::json($post);
    }
}
