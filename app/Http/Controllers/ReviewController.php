<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\User;
use Illuminate\Http\Request;
use Response;

class ReviewController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = User::where('remember_token', $request->token)->first();

        $review = new Review;

        $review->postId = $request->postId;
        $review->reviewTo = $request->reviewTo;
        $review->reviewBy = $user->id;
        $review->review = $request->review;

        $review->save();

        $user = User::where('id', $review->reviewTo)->first();

        $totalEntries = Review::where('reviewTo', $review->reviewTo)->count();
        $sumTotalEntries = Review::where('reviewTo', $review->reviewTo)->sum('review');
        $user->rating = $sumTotalEntries / $totalEntries;

        $user->save();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function isReviewSubmitted(Request $request) {
        $review = Review::where('postId', $request->postId)->first();

        if($review) {
            return Response::json(true);
        } else {
            return Response::json(false);
        }
    }
}
