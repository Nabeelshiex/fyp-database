<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\PostStatus;
use App\User;
use Illuminate\Http\Request;
use Response;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $posts = Post::with('user:id,first_name,last_name,remember_token')->with('bids')->orderBy('id', 'desc')->get();

        // foreach ($posts as $post) {
        //     $post->userName = $post->user->first_name . ' ' . $post->user->last_name;
        // }

        return Response::json($posts, 200);
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

    public function recentPosts(Request $request)
    {
        if ($request->userId) {
            $user = User::where('id', $request->userId)->first();

            $posts = Post::where('user_id', $user->id)->orderBy('id', 'desc')->take(3)->get();

            return Response::json($posts, 200);
        }
        $user = User::where('remember_token', $request->token)->first();

        $posts = Post::where('user_id', $user->id)->orderBy('id', 'desc')->take(3)->get();

        return Response::json($posts, 200);
    }

    public function allUserPosts(Request $request)
    {
        if ($request->userId) {
            $user = User::where('id', $request->userId)->first();

            $posts = Post::where('user_id', $user->id)->get();

            return Response::json($posts, 200);
        }
        $user = User::where('remember_token', $request->token)->first();

        $posts = Post::where('user_id', $user->id)->get();

        return Response::json($posts, 200);
    }

    public function singlePost(Request $request)
    {
        $post = Post::where('id', $request->postId)->with('bidsForSinglePost')->with('user:id,first_name,last_name,image,rating,remember_token')->first();

        return Response::json($post);
    }

    public function addPost(Request $request)
    {
        $user = User::where('remember_token', $request->token)->first();

        if ($user) {
            $post = new Post;

            $post->user_id = $user->id;
            $post->title = $request->title;
            $post->description = $request->description;
            $post->category = 'DUMMY';

            if ($request->file('image')) {

                $image = $request->file('image');
                $name = time() . '.' . $image->getClientOriginalExtension();
                $destinationPath = public_path('/images/posts');
                $image->move($destinationPath, $name);

                $post->image = $name;

                $post->save();

                $post = Post::with('user:id,first_name,last_name,remember_token')->with('bids')->where('id', $post->id)->first();
                $post->userName = $post->user->first_name . ' ' . $post->user->last_name;
                return Response::json($post, 200);
            }

            return Response::json('No image added', 400);
        }
    }

    public function getUserNameForPost(Request $request)
    {
        $user = User::where('id', $request->userId)->first();

        if ($user) {
            return Response::json([
                'name' => $user->first_name . ' ' . $user->last_name,
            ], 200);
        } else {
            return Response::json('Not Found', 404);
        }
    }

    public function getUserForPost(Request $request)
    {
        $user = User::where('id', $request->userId)->first();

        if ($user) {
            return Response::json([
                'name' => $user->first_name . ' ' . $user->last_name,
                'image' => $user->image,
                'description' => $user->description,
                'location' => $user->location,
                'rating' => $user->rating,
                'sameUserVisitProfile' => $user->remember_token == $request->token
            ], 200);
        } else {
            return Response::json('null', 400);
        }
    }

    public function countAllPosts(Request $request)
    {
        $count = Post::get()->count();

        return Response::json($count);
    }
}
