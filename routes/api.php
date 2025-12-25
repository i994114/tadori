<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::resource('categories', 'CategoriesController')->only(['index']);
Route::get('/all-categories', 'CategoriesController@indexAll');

Route::resource('steps', 'StepsController')->only(['index', 'show']);
Route::resource('users', 'UsersController')->only(['show']);

Route::get('/steps-for-sidebar', 'StepsController@getStepsForSidebar');
Route::get('/recent-steps', 'StepsController@getRecentSteps');
Route::get('/step/{id}/user/progresses', 'SubStepsController@userSubStepProgress');
Route::post('/refresh', 'AuthController@refresh');

Route::post('/login', 'AuthController@login');
Route::post('/users', 'UsersController@store');

Route::post('/contact-email', 'ContactController@send');    //お問い合わせメール送信用ルート


// ログインユーザ専用ルート
Route::middleware(['auth:api'])->group(function () {
    Route::post('/logout', 'AuthController@logout');
    Route::get('/me', 'AuthController@me');    

    Route::middleware(['verified'])->group(function () {
        Route::resource('users', 'UsersController')->only(['update', 'destroy']);
        Route::put('/user/{user_id}/password', 'UsersController@changePassword');
        
        //拡張機能準備中
        //Route::put('/user/email', 'UsersController@changeEmail');

        Route::resource('steps', 'StepsController')->only(['store', 'update', 'destroy']);
        Route::get('/step/{id}/sub-steps-sum', 'StepsController@sumSubStepEstimatedTime');
        Route::patch('/step/{id}/restore', 'StepsController@restoreStep');

        Route::resource('sub_steps', 'SubStepsController');
        Route::patch('/sub-step/update-order', 'SubStepsController@updateOrder');
        Route::patch('/sub-step/{id}/restore', 'SubStepsController@restoreSubStep');

        Route::resource('challenges', 'ChallengesController');
        Route::resource('progresses', 'ProgressesController');

        Route::resource('favorites', 'FavoritesController')->only(['store', 'update', 'destroy']);
        Route::delete('/user-favorites/{user_id}/', 'FavoritesController@destroyMyFavorites');

        Route::get('/user/step/{id}/challenge', 'ChallengesController@getUserStepChallenge');
        
        Route::get('/user/challenges', 'ChallengesController@userChallenges');
        Route::get('/user/favorites', 'FavoritesController@userFavorites');
        Route::get('/user/posted-steps', 'StepsController@getMyPostedSteps');

    });

});


// 管理者専用ルート
Route::middleware(['admin.only'])->group(function () {
    Route::resource('categories', 'CategoriesController')->only(['store', 'update', 'destroy']);
    Route::patch('/category/{id}/restore', 'CategoriesController@restore');
});
