<?php

namespace App\Http\Controllers\Auth;

use App\Constants\EmailVerificationStatus;
use App\User;
use App\Store;
use App\Constants\LoginType;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\VerifiesEmails;


class VerificationController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Email Verification Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling email verification for any
    | user that recently registered with the application. Emails may also
    | be re-sent if the user didn't receive the original email message.
    |
    */

    use VerifiesEmails;

    /**
     * Where to redirect users after verification.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('signed')->only('verify');
        if (app()->environment() !== 'local') {
            $this->middleware('throttle:6,1')->only('verify', 'resend');
        }
        
    }

    //SPAのためにオーバーライド
    public function resend(Request $request)
    {
        $user = auth()->user();
        
        if (!$user) {
            return response()->json(['msg' => 'ログインしてください']);
        }

        if ($user->email_verified_at) {
            return response()->json(['msg' => 'すでにメール認証済みです']);
        }

        $user->sendEmailVerificationNotification();

        return response()->json(['msg' => '認証メールを再送しました']);
    }

    public function verify(Request $request, $id)
    {
        $id = intval($id);

        $user = User::findOrFail($id);

        $key = $user->getJWTIdentifier();

        // 判定用のパラメータ
        $status = '';

        // ID不一致
        if ($id !== $key) {
            $status = EmailVerificationStatus::FAILED;
        } elseif ($user->hasVerifiedEmail()) {
                $status = EmailVerificationStatus::ALREADY_VERIFIED;
        } else {
            if ($user->markEmailAsVerified()) {
                event(new Verified($user));
            }
            $status = EmailVerificationStatus::VERIFIED;
            }

        // Bladeに値を渡す
        return view('auth.email_verified', [
            'status' => $status,
        ]);
    }

}
