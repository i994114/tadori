<?php

namespace App\Http\Controllers\Auth;

use App\Constants\LoginType;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Password;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }
    
    //SPAのため、フロントにjsonで返すようにオーバーライド
    protected function sendResetLinkResponse($response)
    {
        return response()->json(['status' => $response]);
    }

    protected function sendResetLinkFailedResponse($response)
    {
        return response()->json(['error' => $response]);
    }

}
