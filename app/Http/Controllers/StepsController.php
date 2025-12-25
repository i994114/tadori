<?php

namespace App\Http\Controllers;

use App\Step;
use Illuminate\Http\Request;
use App\Service\StepSearchService;
use App\Service\ImageUploadService;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\CreateStepRequest;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class StepsController extends Controller
{
    protected $imageUploadService;
    protected $stepSearchService;
    
    public function __construct(ImageUploadService $imageUploadService, StepSearchService $stepSearchService)
    {
        $this->imageUploadService = $imageUploadService;
        $this->stepSearchService = $stepSearchService;
    } 
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        //カテゴリ名、オーナーのユーザ情報をつけてSTEP一覧を取得
        $query = Step::
            with([
                'category' => fn ($q) => $q->withTrashed(),
                'owner' => fn ($q) => $q->withTrashed(),
            ])
            ->withCount('challenges');
        $steps = $this->stepSearchService->searchSteps($request, $query);
        return response()->json($steps);
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
    public function store(CreateStepRequest $request)
    {
        try {
            $step = new Step;

            $this->authorize('create', Step::class);

            $user = auth()->user();

            //画像ファイル有無の判定
            if ($request->step_img) {
                $fileName = $this->imageUploadService->processImageUpload($request->step_img);
                
                //DBに書き込むデータをセット(ファイル名のみを保存)
                $step->step_img = $fileName;
            } else {
                $step->step_img = null;
            }
            
            $step->step_name = $request->step_name;
            $step->step_detail = $request->step_detail;
            $step->owner_id = $user->id;
            $step->category_id = $request->category_id;
            $step->total_estimated_time = $request->total_estimated_time;
            
            $step->save();

            return response()->json($step);
        } catch (AuthorizationException $e) {
            Log::error('Steps store失敗(権限エラー): ' . $e->getMessage(), ['request' => $request->all()]);
            return response()->json([], 403);
        } catch (ModelNotFoundException $e) {
            Log::error('Steps store失敗(モデル未発見): ' . $e->getMessage(), ['request' => $request->all()]);
            return response()->json([], 404);
        } catch (Exception $e) {
            Log::error('Steps store失敗: ' . $e->getMessage(), ['request' => $request->all()]);
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
        $step = Step::withTrashed()
                ->with([
                    'category' => fn ($q) => $q->withTrashed(),
                    'owner' => fn ($q) => $q->withTrashed(),
                ])
                ->findOrFail($id);
        return response()->json($step);
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
    public function update(CreateStepRequest $request, Step $step)
    {
        try {
            $this->authorize('update', $step);

            if ($request->isMethod('put')) {
                if (!empty($request->step_img)) {
                    
                    //もともと画像登録されているか
                    if ($step->step_img) {
                        //古い画像を削除
                        $this->imageUploadService->deleteImage($step->step_img);
                    }

                    //新しい画像を登録し、ファイル名を取得、セット
                    $fileName = $this->imageUploadService->processImageUpload($request->step_img);                //DBに書き込むデータをセット
                    $step->step_img = $fileName;

                } else if ($request->deleted_step_img) {
                    $this->imageUploadService->deleteImage($step->step_img);
                    $step->step_img = null;
                } else {
                }
                
                $step->step_name = $request->step_name;
                $step->step_detail = $request->step_detail;
                $step->category_id = $request->category_id;
                $step->total_estimated_time = $request->total_estimated_time;
                $step->save();

                return response()->json(['msg' => 'STEPを編集しました']);
            }
        } catch (AuthorizationException $e) {
            Log::error('Steps update失敗(権限エラー): ' . $e->getMessage(), ['step' => $step->only('id')]);
            return response()->json([], 403);
        } catch (ModelNotFoundException $e) {
            Log::error('Steps update失敗(モデル未発見): ' . $e->getMessage(), ['step' => $step->only('id')]);
            return response()->json([], 404);
        } catch (Exception $e) {
            Log::error('Steps update失敗: ' . $e->getMessage(), ['step' => $step->only('id')]);
            return response()->json([], 500);
        }


    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Step $step)
    {
        try {
            $this->authorize('delete', $step);
            
            //先に、STEPのもつお気に入りデータを削除
            $step->favorites()->delete();

            //すでにチャレンジ者がいるか
            if ($step->challenges()->exists()) {
                //チャレンジをもったSTEPのため、論理削除
                $step->subSteps()->delete();
                $step->delete();
            
                $msg = 'STEPを削除しました(チャレンジ中のユーザには記録が残ります)';
            } else {
                //チャレンジをもってないので、物理削除
                $step->subSteps()->forceDelete();
                $step->forceDelete();
            
                $msg = 'STEPを完全に削除しました';
            }
            
            return response()->json(['msg' => $msg]);
        } catch (AuthorizationException $e) {
            Log::error('Steps destroy失敗(権限エラー): ' . $e->getMessage(), ['step' => $step]);
            return response()->json([], 403);
        } catch (ModelNotFoundException $e) {
            Log::error('Steps destroy失敗(モデル未発見): ' . $e->getMessage(), ['step' => $step]);
            return response()->json([], 404);
        } catch (Exception $e) {
            Log::error('Steps destroy失敗: ' . $e->getMessage(), ['step' => $step]);
            return response()->json([], 500);
        }

    }

    //論理削除したSTAPの復活処理
    public function restoreStep($id)
    {
        try {
            $user = auth()->user();
            $step = Step::withTrashed()->findOrFail($id);

            if ($user->id === $step->owner_id) {
                $step->deleted_at = null;
                $step->save();

                return response()->json(['msg' => 'STEPを復活しました。現在、子STEPは削除状態です。必要に応じて子STEPも復活してください。']);
            } else {
                return response()->json([], 500);
            }
        } catch (AuthorizationException $e) {
            Log::error('Steps restoreStep失敗(権限エラー): ' . $e->getMessage(), ['id' => $id]);
            return response()->json([], 403);
        } catch (ModelNotFoundException $e) {
            Log::error('Steps restoreStep失敗(モデル未発見): ' . $e->getMessage(), ['id' => $id]);
            return response()->json([], 404);
        } catch (Exception $e) {
            Log::error('Steps restoreStep失敗: ' . $e->getMessage(), ['id' => $id]);
            return response()->json([], 500);
        }

    }

    //子STEPの目標達成時間の和を算出
    public function sumSubstepEstimatedTime($id)
    {
        $step = Step::findOrFail($id);
        $sum = $step->subSteps()->sum('estimated_time');

        return response()->json($sum);
    }

    //投稿したSTEP一覧
    public function getMyPostedSteps(Request $request)
    {
        try {
            $user = auth()->user();
            $query = Step::withTrashed()
                ->where('owner_id', $user->id)
                ->with('category', 'owner')
                ->withCount('challenges')
                ->orderBy('created_at', 'desc');

            $steps = $this->stepSearchService->searchSteps($request, $query);

            return response()->json($steps);
        } catch (AuthorizationException $e) {
            Log::error('Steps getMyPostedSteps失敗(権限エラー): ' . $e->getMessage());
            return response()->json([], 403);
        } catch (ModelNotFoundException $e) {
            Log::error('Steps getMyPostedSteps失敗(モデル未発見): ' . $e->getMessage());
            return response()->json([], 404);
        } catch (Exception $e) {
            Log::error('Steps getMyPostedSteps失敗: ' . $e->getMessage());
            return response()->json([], 500);
        }


    }

    //トップページ用に最新のSTEP情報を取得
    public function getRecentSteps()
    {
        try {
            $steps = Step::withTrashed()
                ->with([
                    'category' => fn ($q) => $q->withTrashed(),
                    'owner' => fn ($q) => $q->withTrashed(),
                ])
                ->orderBy('created_at', 'desc')
                ->take(12)
                ->get();

            return response()->json($steps);
        } catch (AuthorizationException $e) {
            Log::error('Steps getRecentSteps失敗(権限エラー): ' . $e->getMessage());
            return response()->json([], 403);
        } catch (ModelNotFoundException $e) {
            Log::error('Steps getRecentSteps失敗(モデル未発見): ' . $e->getMessage());
            return response()->json([], 404);
        } catch (Exception $e) {
            Log::error('Steps getRecentSteps失敗: ' . $e->getMessage());
            return response()->json([], 500);
        }

    }
}
