<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


/*
Route::get('/', function () {
    return view('welcome');
});
*/

//パスワードリセット関連
// パスワードリセットメール送信
Route::post('/password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
// パスワードリセット処理
Route::post('/password/reset', 'Auth\ResetPasswordController@reset')->name('password.update');
// パスワードリセットトークン確認画面表示
Route::get('/password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');

// メール認証
Route::get('/email/verify/{id}/{hash}', 'Auth\VerificationController@verify')->name('verification.verify');
Route::get('/email/resend', 'Auth\VerificationController@resend')->name('verification.resend');

Route::middleware('verified')->group(function() {
    //本登録ユーザだけが表示できるページ
    Route::get('verified', function() {
        return '本登録が完了しています';
    });
});

Route::get('/home', 'HomeController@index')->name('home');

//SPAのため、トップを固定するための設定
Route::middleware(['web'])->get('/{any}', function() {
    return view('welcome');
})->where('any', '.*');






