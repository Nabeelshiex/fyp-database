<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Response;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::get();
        return Response::json($users);
    }

    // public function login(Request $request)
    // {
    //     $user = User::where('email', $request->email)->first();

    //     $user->remember_token = $request->access_token;
    //     $user->save();

    //     $result = [
    //         "name" => $user->first_name . ' ' . $user->last_name,
    //         "token" => $user->remember_token,
    //         "image" => $user->image
    //     ];

    //     return Response::json($result, 200);
    // }

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
        return Response::json(['message' => "You are not allowed to register"], 200);

        $validator = User::storeValidate($request);

        if (!$validator->fails()) {
            $user = new User;

            $user->first_name = $request->firstName;
            $user->last_name = $request->lastName;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);

            $user->save();

            return Response::json(['message' => 'success'], 200);
        } else {
            return Response::json($validator->errors(), 400);
        }
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

    public function getUser(Request $request)
    {
        $user = User::where('remember_token', $request->token)->first();

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

    public function uploadLocation(Request $request)
    {
        if ($request->location) {

            $user = User::where('remember_token', $request->token)->first();

            $user->location = $request->location;

            $user->save();

            return Response::json($user->location, 200);
        }

        return Response::json('No location', 400);
    }

    public function uploadDescription(Request $request)
    {
        if ($request->description) {

            $user = User::where('remember_token', $request->token)->first();

            $user->description = $request->description;

            $user->save();

            return Response::json($user->description, 200);
        }

        return Response::json('No description', 400);
    }

    public function uploadImage(Request $request)
    {
        if ($request->file('image')) {

            $user = User::where('remember_token', $request->token)->first();

            if ($user->image != null) {
                unlink(public_path($user->image));
                $user->image = null;

                $user->save();
            }

            $image = $request->file('image');
            $name = time() . '.' . $image->getClientOriginalExtension();
            $destinationPath = public_path('/images/users');
            $image->move($destinationPath, $name);

            $user->image = $name;

            $user->save();

            return Response::json($user->image, 200);
        }

        return Response::json('No image', 400);
    }

    public function updatePassword(Request $request)
    {
        $user = User::where('remember_token', $request->token)->first();

        if (Hash::check($request->oldPassword, $user->password)) {
            $user->password = Hash::make($request->newPassword);

            $user->save();

            return Response::json('success', 200);
        }

        return Response::json('Incorrect password', 400);
    }

    public function getImage(Request $request)
    {
        $user = User::where('remember_token', $request->token)->first();

        return Response::json($user->image);
    }
}
