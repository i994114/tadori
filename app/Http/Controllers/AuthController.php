<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;

//Tymon JWTのトークンをつかうログイン、ログアウト用
class AuthController extends Controller
{
    //ログイン操作時にアクセストークン算出用
    public function login(Request $request) {
        $credentials = request(['email', 'password']);

        //Remember Meが有効かチェック
        $remember = $request->remember;

        //アクセストークン有効期限の設定
        $ttl = $remember
                ? Config::get('jwt.refresh_ttl') / 2       // remember時：refresh_ttlの半分である7日とする
                : JWTAuth::factory()->getTTL();            // 通常：jwt.phpのttl

        //アクセストークンを算出
        if (!$access_token = auth()->setTTL($ttl)->attempt($credentials)) {
            return response()->json(['errors' => ['general' => ['ログイン情報が間違っています。']]], 401);
        }
        
        //response用のメソッドをコール
        return $this->respondWithToken($access_token, $ttl);
    }

    //ログアウト
    public function logout(Request $request) {

        try {
            auth()->logout();
            return response()->json(['message' => 'ログアウトしました']);
        } catch(\Exception $e) {
            return response()->json([
                'messae' => 'ログアウトに失敗しました',
                'error' => $e->getMessage()
            ]);
        }
    }

    //ログイン情報取得
    public function me(Request $request) {
        try {
            $user = auth()->user();

            if (!$user) {
                return response()->json([], 401);
            }

            return response()->json($user);
        } catch(\Exception $e) {
            return response()->json([
                'messae' => 'ログイン情報取得(/me)に失敗しました',
                'error' => $e->getMessage()
            ]);
        }
    }

    //リフレッシュ処理
    public function refresh(Request $request) {
        //curlにて直接URL指定による悪意のあるトークンリフレッシュを防ぐため、router.jsからのリクエストかを判定してからトークン更新
        if ($request->refreshTokenRequest) {
            try {
                $token = auth()->refresh();
                $ttl = JWTAuth::factory()->getTTL();
                return $this->respondWithToken($token, $ttl);
            } catch (TokenExpiredException $e) {
                Log::warning('Token refresh期限切れ', ['request' => $request->all()]);
                return response()->json([], 401);
            } catch (\Exception $e) {
                Log::error('Token refresh失敗: ' . $e->getMessage(), ['request' => $request->all()]);
                return response()->json([], 500);
            }
            
        }
    }

    protected function respondWithToken($token, $ttl) {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => $ttl * 60,  //OAuthにあわせて、秒に変換
            'refresh_token_expires_in' => Config::get('jwt.refresh_ttl')
        ]);
    }
}
