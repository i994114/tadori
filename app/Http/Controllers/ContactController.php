<?php

namespace App\Http\Controllers;

use App\User;
use App\Constants\UserType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\ContactRequest;

class ContactController extends Controller
{
    //お問い合わせメール送信
    public function send(ContactRequest $request) {
        try {
            if ($request->type === 'contact') {    //トップページからのお問い合わせ時

                //管理者権限のユーザ情報を取得
                $admin_user = User::where('role', UserType::ADMIN)->first();
                
                if ($admin_user) {

                    //管理者用データ
                    $adminMailData = [
                        'admin_name' => $admin_user->name,
                        'inquired_name' => $request->name,
                        'email' => $request->email,
                        'comment' => $request->message
                    ];

                    //管理者に送信
                    Mail::send('emails.contact_admin', $adminMailData, function($message) use ($admin_user) {
                        $message->to($admin_user->email)
                                ->subject('問い合わせがありました');
                    });

                    //利用者用データ
                    $userMailData = [
                        'name' => $request->name,
                        'comment' => $request->message
                    ];

                    //利用者に送信
                    Mail::send('emails.contact_user', $userMailData, function($message) use ($request) {
                        $message->to($request->email)
                                ->subject('お問い合わせありがとうございます');
                    });

                    return response()->json(['msg' => 'お問い合わせを送信しました。']);
                } else {
                    return response()->json(['msg' => '送信先の管理者が見つかりません。後でもう一度お試しください。']);
                }
            }  else {
                return response()->json(['msg' => '不正な操作です。']);
            }
        } catch(\Exception $e) {
            Log::error('ContactController send失敗: ' .$e->getMessage(), ['request' => $request->all()]);
            return response()->json([], 500);
        }
    }
}
