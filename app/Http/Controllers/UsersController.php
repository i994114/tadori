<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use App\Service\ImageUploadService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\EditUserRequest;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\ChangeEmailRequest;
use App\Http\Requests\ChangePasswordRequest;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UsersController extends Controller
{
    protected $imageUploadService;

    public function __construct(ImageUploadService $imageUploadService)
    {  
        $this->imageUploadService = $imageUploadService;
    }

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
    public function store(CreateUserRequest $request)
    {
        try {
            $user = new User;

            $user->name = $request->name;
            $user->email = $request->email;
            $user->password =  Hash::make($request->password);
            
            $user->save();

            return response()->json(['msg' => 'ユーザ登録しました。続けてメール認証してください。']);
        } catch (AuthorizationException $e) {
            Log::error('Users store失敗(権限エラー): ' . $e->getMessage());
            return response()->json([], 403);
        } catch (\Exception $e) {
            Log::error('Users store失敗:' .$e->getMessage());
            return response()->json([], 500);
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
        try {
            //ログインユーザ
            $user = auth()->user();
            
            //リクエストのあったユーザ情報
            $requestedUser = User::withTrashed()->findOrFail($id);

            //リクエストの対象ユーザは、ログインユーザか
            if (!$user || $user->id !== (int)$id) {
                //リクエストユーザは本人でないため、返信情報を限定する
                $limitedUser = $requestedUser->only(['name', 'user_profile', 'user_img', 'created_at']);
                return response()->json($limitedUser);

            }

            //本人の場合は全情報返却
            return response()->json($requestedUser);

        } catch (AuthorizationException $e) {
            Log::error('Users show失敗(権限エラー): ' . $e->getMessage(), ['id' => $id]);
            return response()->json([], 403);
        } catch (ModelNotFoundException $e) {
            Log::error('Users show失敗(モデル未発見): ' . $e->getMessage(), ['id' => $id]);
            return response()->json([], 404);
        } catch (\Exception $e) {
            Log::error('Users show失敗:' .$e->getMessage(), ['id' => $id]);
            return response()->json([], 500);
        }

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
    public function update(EditUserRequest $request, User $user)
    {
        try {
            $this->authorize('update', $user);
            
            if (!empty($request->user_img)) {

                //もともと画像登録されているか
                if ($user->getOriginal('user_img')) {
                    //古い画像を削除
                    $this->imageUploadService->deleteImage($user->getOriginal('user_img'));
                }


                //新しい画像を登録
                $fileName = $this->imageUploadService->processImageUpload($request->user_img);
                $user->user_img = $fileName;

            } else if ($request->deleted_user_img) {
                //ストレージから画像ファイルを削除
                $this->imageUploadService->deleteImage($user->getOriginal('user_img'));
                //DBからも削除
                $user->user_img = null;
            } else {
            }

            $user->name = $request->name;
            $user->user_profile = $request->user_profile;
            $user->save();

            return response()->json(['msg' => 'ユーザプロフィールを変更しました']);
        } catch (AuthorizationException $e) {
            Log::error('Users update(権限エラー): ' . $e->getMessage(), ['user' => $user->only('id')]);
            return response()->json([], 403);
        } catch (ModelNotFoundException $e) {
            Log::error('Users update(モデル未発見): ' . $e->getMessage(), ['user' => $user->only('id')]);
            return response()->json([], 404);
        } catch(\Exception $e) {
            Log::error('Users update失敗:' .$e->getMessage(), ['user' => $user->only('id')]);
            return response()->json([], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        try {
            $this->authorize('delete', $user);

            //進捗情報を削除
            $user->progresses()->delete();
            //チャレンジ情報を削除
            $user->userChallenges()->delete();
            //お気に入りを削除
            $user->favorites()->forceDelete();
            //さいごにユーザを削除
            //※STEPおよび子STEPは削除しない(オーナーはいなくてもチャレンジ可能にするため)
            $user->delete();

            return response()->json(['msg' => '退会しました。ご利用ありがとうございます。']);
        } catch (AuthorizationException $e) {
            Log::error('Users destroy(権限エラー): ' . $e->getMessage(), ['user' => $user->only('id')]);
            return response()->json([], 403);
        } catch (ModelNotFoundException $e) {
            Log::error('Users destroy(モデル未発見): ' . $e->getMessage(), ['user' => $user->only('id')]);
            return response()->json([], 404);
        } catch(\Exception $e) {
            Log::error('Users destroy失敗:' .$e->getMessage(), ['user' => $user->only('id')]);
            return response()->json([], 500);
        }

    }

    //パスワード変更
    public function changePassword(ChangePasswordRequest $request, $id)
    {
        try {
            $user = User::findOrFail($id);
            $user->password = Hash::make($request->new_password);
            $user->save();
        } catch (AuthorizationException $e) {
            Log::error('Users changePassword失敗(権限エラー): ' . $e->getMessage(), ['user' => $user->only('id')]);
            return response()->json([], 403);
        } catch (ModelNotFoundException $e) {
            Log::error('Users changePassword失敗(モデル未発見): ' . $e->getMessage(), ['user' => $user->only('id')]);
            return response()->json([], 404);
        } catch(\Exception $e) {
            Log::error('Users changePassword失敗:' .$e->getMessage(), ['id' => $id]);
            return response()->json([], 500);
        }

    }

    //アドレス変更
    public function changeEmail(ChangeEmailRequest $request)
    {
        try {
            $user = auth()->user();

            if (!$user) {
                return response()->json(['msg' => '認証ユーザーが見つかりません'], 401);
            }

            $user->email = $request->email;
            $user->email_verified_at = null;    //アドレス変更にともなう、メール再認証のためクリア
            $user->save();

            return response()->json(['msg' => '登録メールアドレスを変更しました。ログインし、メール認証してください。']);
        } catch (AuthorizationException $e) {
            Log::error('Users changeEmail失敗(権限エラー): ' . $e->getMessage(), ['user_id' => $user->id ?? null]);
            return response()->json([], 403);
        } catch (ModelNotFoundException $e) {
            Log::error('Users changeEmail失敗(モデル未発見): ' . $e->getMessage(), ['user_id' => $user->id ?? null]);
            return response()->json([], 404);
        } catch(\Exception $e) {
            Log::error('Users changeEmail失敗:' .$e->getMessage(), ['user_id' => $user->id ?? null]);
            return response()->json([], 500);
        }

        
    }
}
