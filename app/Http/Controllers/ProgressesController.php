<?php

namespace App\Http\Controllers;

use App\SubStep;
use App\Progress;
use App\Challenge;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ProgressesController extends Controller
{
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
    public function store(Request $request)
    {
        try {
            //ログインユーザの、当該子STEPのチャレンジidを取得
            $user = auth()->user();

            //ログインユーザの当該STEPのチャレンジを取得
            $challenge = Challenge::where('user_id', $user->id)
                ->where('step_id', $request->stepId)
                ->first();
            //Policyチェック
            $this->authorize('create', $challenge);

            //過去に当該子STEPをクリアしたかを検索
            $progress = Progress::withTrashed()
                ->where('sub_step_id', $request->subStepId)
                ->where('challenge_id', $challenge->id)
                ->first();

            if ($progress) {
                if ($progress->trashed()) {
                    //クリアキャンセルした進捗を復活(再度クリアに)
                    $progress->restore();
                } else {
                    return response()->json(['msg' => 'すでにクリア済みです']);
                }
            } else {
                //新規追加
                $progress = new Progress();
                $progress->sub_step_id = $request->subStepId;
                $progress->challenge_id = $challenge->id;
                
                $progress->save();
            }

            return response()->json(['msg' => 'クリア成功！次の子STEPへ進んでください！']);
        } catch (AuthorizationException $e) {
            Log::error('Progress store失敗(権限エラー): ' . $e->getMessage(), ['request' => $request->all()]);
            return response()->json([], 403);
        } catch (ModelNotFoundException $e) {
            Log::error('Progress store失敗(モデル未発見): ' . $e->getMessage(), ['request' => $request->all()]);
            return response()->json([], 404);
        } catch (Exception $e) {
            Log::error('Progress store失敗: ' . $e->getMessage(), ['request' => $request->all()]);
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
    public function destroy(Progress $progress)
    {
        try {
            //ログインユーザの当該STEPのチャレンジを取得
            $challenge = Challenge::findOrFail($progress->challenge_id);

            $this->authorize('delete', $challenge);
            $progress->delete();

            return response()->json(['msg' => 'クリアをキャンセルしました']);
        } catch (AuthorizationException $e) {
            Log::error('Progress destroy失敗(権限エラー): ' . $e->getMessage(), ['progress' => $progress]);
            return response()->json([], 403);
        } catch (ModelNotFoundException $e) {
            Log::error('Progress destroy失敗(モデル未発見): ' . $e->getMessage(), ['progress' => $progress]);
            return response()->json([], 404);
        } catch (Exception $e) {
            Log::error('Progress destroy失敗: ' . $e->getMessage(), ['progress' => $progress]);
            return response()->json([], 500);
        }

    }

}
