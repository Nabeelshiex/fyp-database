<?php

use App\Http\Controllers\PostController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::resource('users', 'UserController');

Route::group(['prefix' => 'auth'], function () {
    Route::post('login', 'AuthController@login');
    Route::post('logout', 'AuthController@logout');
    Route::post('refresh', 'AuthController@refresh');
    Route::post('me', 'AuthController@me');

    Route::group(['prefix' => 'user', 'middleware' => ['custom.auth']], function () {
        Route::get('get-user', 'UserController@getUser');
        Route::post('upload-image', 'UserController@uploadImage');
        Route::post('upload-location', 'UserController@uploadLocation');
        Route::post('upload-description', 'UserController@uploadDescription');
        Route::post('update-password', 'UserController@updatePassword');
        Route::get('get-image', 'UserController@getImage');
    });

    Route::group(['prefix' => 'post', 'middleware' => ['custom.auth']], function () {
        Route::post('recent', 'PostController@recentPosts');
        Route::post('user-all', 'PostController@allUserPosts');
        Route::post('single', 'PostController@singlePost');
        Route::post('add', 'PostController@addPost');
        Route::resource('', 'PostController');
        Route::post('get-username', 'PostController@getUserNameForPost');
        Route::post('get-user', 'PostController@getUserForPost');
        Route::get('count', 'PostController@countAllPosts');
    });

    Route::group(['prefix' => 'message', 'middleware' => ['custom.auth']], function () {
        Route::get('get-users', 'MessageController@getUsersListForMessages');
        Route::post('get-messages', 'MessageController@getMessagesByUser');
        Route::get('get-logged-in-user-id', 'MessageController@getLoggedInUserId');
        Route::post('add', 'MessageController@addMessage');
        Route::get('unread-count', 'MessageController@getUnReadCount');
        Route::post('read-count', 'MessageController@setMessagesRead');
    });

    Route::group(['prefix' => 'bid', 'middleware' => ['custom.auth']], function () {
        Route::post('', 'BidController@getBids');
        Route::post('add', 'BidController@addBid');
        Route::post('accepted', 'BidController@bidAccepeted');
    });

    Route::group(['prefix' => 'payment', 'middleware' => ['custom.auth']], function () {
        Route::get('', 'CheckOutController@initilizeBrainTree');
        Route::post('checkout', 'CheckOutController@confirmBrainTree');
    });

    Route::group(['prefix' => 'earning', 'middleware' => ['custom.auth']], function () {
        Route::get('total', 'EarningController@totalEarning');
        Route::get('pending', 'EarningController@pendingClearence');
        Route::get('withdraw', 'EarningController@withdraw');
        Route::get('', 'EarningController@index');
    });

    Route::group(['prefix' => 'review', 'middleware' => ['custom.auth']], function () {
        Route::get('check-review', 'ReviewController@isReviewSubmitted');
        Route::resource('', 'ReviewController');
    });
});
