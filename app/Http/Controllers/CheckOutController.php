<?php

namespace App\Http\Controllers;

use App\Models\Bid;
use App\Models\Earning;
use App\Models\Expense;
use App\Models\Post;
use App\Models\Transaction;
use App\User;
use Braintree;
use Illuminate\Http\Request;
use Response;

class CheckOutController extends Controller
{
    public function initilizeBrainTree()
    {
        $gateway = new Braintree\Gateway([
            'environment' => 'sandbox',
            'merchantId' => 'kqrtrb877kphcpyd',
            'publicKey' => '9hwvbs9ys5zk7678',
            'privateKey' => '54eeaaf94f11b90fcb489172b4d5999a'
        ]);

        $clientToken = $gateway->clientToken()->generate();

        return Response::json($clientToken);
    }

    public function confirmBrainTree(Request $request)
    {
        $user = User::where('remember_token', $request->token)->first();
        $bid = Bid::where('postId', $request->postId)->where('userId', $user->id)->orderBy('id', 'desc')->first();

        $gateway = new Braintree\Gateway([
            'environment' => 'sandbox',
            'merchantId' => 'kqrtrb877kphcpyd',
            'publicKey' => '9hwvbs9ys5zk7678',
            'privateKey' => '54eeaaf94f11b90fcb489172b4d5999a'
        ]);

        $amount = $bid->price;
        $nonce = $request->nonce;

        $result = $gateway->transaction()->sale([
            'amount' => $amount,
            'paymentMethodNonce' => $nonce,
            'options' => [
                'submitForSettlement' => true
            ],
            'customer' => [
                'id' => $user->id,
                'firstName' => $user->first_name,
                'lastName' => $user->last_name,
                'email' => $user->email
            ]
        ]);

        if ($result->success) {
            $post = Post::where('id', $request->postId)->first();
            $post->isPaid = true;

            $transaction = new Transaction;

            $transaction->postId = $post->id;
            $transaction->senderId = $user->id;
            $transaction->receiverId = $post->user_id;
            $transaction->amount = $amount;

            $earning = new Earning;

            $earning->userId = $post->user_id;
            $earning->totalEarned = $amount;
            $earning->pendingClearence = $amount;

            $expense = new Expense;

            $expense->userId = $user->id;
            $expense->amount = $amount;

            $transaction->save();
            $post->save();
            $earning->save();
            $expense->save();
        }

        return Response::json($result);
    }
}
