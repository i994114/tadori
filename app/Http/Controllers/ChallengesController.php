<?php

namespace App\Http\Controllers;

use App\Challenge;
use Illuminate\Http\Request;
use App\Service\StepSearchService;
use Illuminate\Support\Facades\Log;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ChallengesController extends Controller
{
    protected $stepSearchService;

    public function __construct(StepSearchService $stepSearchService)
    {
        $this->stepSearchService = $stepSearchService;
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
    public function store(Request $request)
    {
        try {
            $user = auth()->user();

            //過去にクリア済みか検索
            $challenge = Challenge::withTrashed()
                ->where('user_id', $user->id)
                ->where('step_id', $request->step_id)
                ->first();

            if ($challenge) {
                //論理削除されたレコードか
                if ($challenge->trashed()) {
                    $challenge->restore();
                } else {
                    return response()->json(['msg' => 'すでにチャレンジ済みです']);
                } 
            } else {

                $this->authorize('create', Challenge::class);
                
                //過去にクリア済みでないので、進捗を新規作成
                $challenge = new Challenge();
                $challenge->step_id = $request->step_id;
                $challenge->user_id = $user->id;

                $challenge->save();
            }

            return response()->json(['msg' => 'チャレンジを登録しました。まずは最初の子STEPから取り組みましょう。']);
        } catch (AuthorizationException $e) {
            Log::error('Challenges store失敗(権限エラー): ' . $e->getMessage(), ['challenge' => $challenge ?? null]);
            return response()->json([], 403);
        } catch (ModelNotFoundException $e) {
            Log::error('Challenges store失敗(モデル未発見): ' . $e->getMessage(), ['challenge' => $challenge ?? null]);
            return response()->json([], 404);
        } catch (Exception $e) {
            Log::error('Challenges store失敗: ' . $e->getMessage(), ['challenge' => $challenge ?? null]);
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
    public function destroy(Challenge $challenge)
    {
        try {
            $this->authorize('delete', $challenge);

            //チャレンジのみ削除し、進捗はそのままにする(再チャレンジ時にもとの進捗を再現したいため)
            $challenge->delete();
            return response()->json(['msg' => 'チャレンジ情報を削除しました。']);
        } catch (AuthorizationException $e) {
            Log::error('Challenges destroy失敗(権限エラー): ' . $e->getMessage(), ['challenge' => $challenge]);
            return response()->json([], 403);
        } catch (ModelNotFoundException $e) {
            Log::error('Challenges destroy失敗(モデル未発見): ' . $e->getMessage(), ['challenge' => $challenge]);
            return response()->json([], 404);
        } catch (Exception $e) {
            Log::error('Challenges destroy失敗: ' . $e->getMessage(), ['challenge' => $challenge]);
            return response()->json([], 500);
        }

    }

    //当該ユーザのチャレンジしているすべてのSTEP情報を取得
    public function userChallenges(Request $request)
    {
        $user = auth()->user();
        $query = $user->challenges()
                    ->withTrashed()
                    ->withPivot('id', 'created_at')
                    ->with([
                        'category' => fn ($q) => $q->withTrashed(),
                        'owner' => fn ($q) => $q->withTrashed(),
                    ])
                    ->withCount('challenges')
                    ->orderBy('pivot_created_at', 'desc');
        $steps = $this->stepSearchService->searchSteps($request, $query);
        return response()->json($steps);
    }

    //単一のSTEPに対する当該ユーザのチャレンジ情報を取得
    public function getUserStepChallenge($id)
    {
        try {
            $user = auth()->user();
            $challenge = Challenge::where('step_id', $id)->where('user_id', $user->id)->first();

            return response()->json($challenge);
        } catch (AuthorizationException $e) {
            Log::error('getUserStepChallenge失敗(権限エラー): ' . $e->getMessage(), ['id' => $id]);
            return response()->json([], 403);
        } catch (ModelNotFoundException $e) {
            Log::error('getUserStepChallenge失敗(モデル未発見): ' . $e->getMessage(), ['id' => $id]);
            return response()->json([], 404);
        } catch (Exception $e) {
            Log::error('getUserStepChallenge失敗: ' . $e->getMessage(), ['id' => $id]);
            return response()->json([], 500);
        }

    }
}
