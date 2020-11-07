<?php

namespace App\Http\Controllers;

use App\Models\Earning;
use App\User;
use Illuminate\Http\Request;
use Response;

class EarningController extends Controller
{
    public function totalEarning(Request $request)
    {
        $user = User::where('remember_token', $request->token)->first();

        $result = [
            'Jan' => 0,
            'Feb' => 0,
            'Mar' => 0,
            'Apr' => 0,
            'May' => 0,
            'Jun' => 0,
            'Jul' => 0,
            'Aug' => 0,
            'Sep' => 0,
            'Oct' => 0,
            'Nov' => 0,
            'Dec' => 0,
        ];

        $earnigs = Earning::select('totalEarned', 'created_at')->where('userId', $user->id)->get();

        foreach ($earnigs as $earnig) {
            $result[$earnig->created_at->format('M')] = $earnig->totalEarned;
        }

        return Response::json($result);
    }

    public function pendingClearence(Request $request)
    {
        $user = User::where('remember_token', $request->token)->first();

        $result = [
            'Jan' => 0,
            'Feb' => 0,
            'Mar' => 0,
            'Apr' => 0,
            'May' => 0,
            'Jun' => 0,
            'Jul' => 0,
            'Aug' => 0,
            'Sep' => 0,
            'Oct' => 0,
            'Nov' => 0,
            'Dec' => 0,
        ];

        $earnigs = Earning::select('pendingClearence', 'created_at')->where('userId', $user->id)->get();

        foreach ($earnigs as $earnig) {
            $result[$earnig->created_at->format('M')] = $earnig->pendingClearence;
        }

        return Response::json($result);
    }

    public function withdraw(Request $request)
    {
        $user = User::where('remember_token', $request->token)->first();

        $result = [
            'Jan' => 0,
            'Feb' => 0,
            'Mar' => 0,
            'Apr' => 0,
            'May' => 0,
            'Jun' => 0,
            'Jul' => 0,
            'Aug' => 0,
            'Sep' => 0,
            'Oct' => 0,
            'Nov' => 0,
            'Dec' => 0,
        ];

        $earnigs = Earning::select('withdraw', 'created_at')->where('userId', $user->id)->get();

        foreach ($earnigs as $earnig) {
            $result[$earnig->created_at->format('M')] = $earnig->withdraw;
        }

        return Response::json($result);
    }

    public function index(Request $request)
    {
        $user = User::where('remember_token', $request->token)->first();

        $totalEarned = $pendingClearence = $withdraw = Earning::where('userId', $user->id);

        $result = [
            'totalEarned' => $totalEarned->sum('totalEarned'),
            'pendingClearence' => $pendingClearence->sum('pendingClearence'),
            'withdraw' => $withdraw->sum('withdraw')
        ];

        return Response::json($result);
    }
}
