<?php

namespace App\Http\Controllers;

use App\Step;
use Exception;
use App\SubStep;
use Illuminate\Http\Request;
use App\Service\ImageUploadService;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\CreateSubStepRequest;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class SubStepsController extends Controller
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
        $subSteps = SubStep::all();
        return response()->json($subSteps, 200);
        
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
    public function store(CreateSubStepRequest $request)
    {
        try {

            $user = auth()->user();
            $step = Step::findOrFail($request->step_id);

            $this->authorize('createSubStep', $step);

            $subStep = new SubStep;

            $user = auth()->user();
            
            //当該STEPの一番うしろの値を取得
            $step = Step::findOrFail($request->step_id);
            $count = $step->subSteps()->count();

            $subStep->sub_step_name = $request->sub_step_name;
            $subStep->step_id = $request->step_id;
            $subStep->order_no = $count + 1; //子STEP登録時はひとまず一番後ろの値とする

            $subStep->save();

            return response()->json($subStep);
        } catch (AuthorizationException $e) {
            Log::error('sub steps store 権限エラー:' . $e->getMessage());
            return response()->json([], 403);
        } catch (ModelNotFoundException $e) {
            Log::error('sub steps store モデル未発見:' . $e->getMessage());
            return response()->json([], 404);
        } catch (Exception $e) {
            Log::error('sub steps store 失敗:' . $e->getMessage(), ['request' => $request->all()]);
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
        $subStep = SubStep::withTrashed()
        ->with(['step.owner', 'step.category'])->findOrFail($id);

        $subStepArray = $subStep->toArray();

        // stepのownerとcategoryを平坦化
        $subStepArray['owner'] = optional($subStep->step)
            ? optional($subStep->step->owner()->withTrashed()->first())->toArray()
            : null;

        $subStepArray['category'] = optional($subStep->step)
            ? optional($subStep->step->category()->withTrashed()->first())->toArray()
            : null;

        // 不要になった step 情報は削除
        unset($subStepArray['step']);

        return response()->json($subStepArray);
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
    public function update(CreateSubStepRequest $request, SubStep $subStep)
    {
        try {
            $step = $subStep->step;
            $this->authorize('update', $step);

            if ($request->isMethod('put')) {
                if (!empty($request->sub_step_img)) {
                    
                    //もともと画像登録されているか
                    if ($subStep->sub_step_img) {
                        //古い画像を削除
                        $this->imageUploadService->deleteImage($subStep->sub_step_img);
                    }

                    //新しい画像を登録し、ファイル名を取得、セット
                    $fileName = $this->imageUploadService->processImageUpload($request->sub_step_img);                //DBに書き込むデータをセット
                    $subStep->sub_step_img = $fileName;

                } else if ($request->deleted_sub_step_img) {
                    $this->imageUploadService->deleteImage($subStep->sub_step_img);
                    $subStep->sub_step_img = null;
                } else {
                }
                
                $subStep->sub_step_name = $request->sub_step_name;
                $subStep->sub_step_detail = $request->sub_step_detail;
                $subStep->estimated_time = $request->estimated_time;
                $subStep->save();

                return response()->json(['msg' => '子STEPを編集しました']);
            }
        } catch (AuthorizationException $e) {
            Log::error('SubSteps update 権限エラー:' . $e->getMessage(), ['subStep' => $subStep->only('id')]);
            return response()->json([], 403);
        } catch (ModelNotFoundException $e) {
            Log::error('SubSteps update モデル未発見:' . $e->getMessage(), ['subStep' => $subStep->only('id')]);
            return response()->json([], 404);
        } catch (Exception $e) {
            Log::error('SubSteps update 失敗:' . $e->getMessage(), ['subStep' => $subStep->only('id')]);
            return response()->json([], 500);
        }


    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(SubStep $subStep)
    {
        try {
            $step = $subStep->step;
            $this->authorize('delete', $step);
            
            if ($subStep->progresses()->exists()) {
                //クリアしたユーザがいるので論理削除
                $subStep->delete();
                $msg = '子STEPを削除しました(チャレンジ中のユーザには記録が残ります)';
            } else {
                //クリアしたユーザがいないので物理削除
                $subStep->forceDelete();
                $msg = '子STEPを完全に削除しました';
            }
            return response()->json(['msg' => $msg]);
        } catch (AuthorizationException $e) {
            Log::error('SubSteps destroy 権限エラー:' . $e->getMessage());
            return response()->json([], 403);
        } catch (ModelNotFoundException $e) {
            Log::error('SubSteps destroy モデル未発見:' . $e->getMessage());
            return response()->json([], 404);
        } catch (Exception $e) {
            Log::error('SubSteps destroy 失敗:' . $e->getMessage(), ['subStep' => $subStep]);
            return response()->json([], 500);
        }

    }

    //論理削除したSTAPの復活処理
    public function restoreSubStep($id)
    {
        try {
            $user = auth()->user();
            $subStep = SubStep::withTrashed()->findOrFail($id);
            $step = $subStep->step;
            $this->authorize('update', $step);
            
            $subStep->deleted_at = null;
            $subStep->save();

            return response()->json(['msg' => '削除した子STEPを復活させました']);
        } catch (AuthorizationException $e) {
            Log::error('substep restore 権限エラー:' . $e->getMessage());
            return response()->json([], 403);
        } catch (ModelNotFoundException $e) {
            Log::error('substep restore モデル未発見:' . $e->getMessage());
            return response()->json([], 404);
        } catch (Exception $e) {
            Log::error('substep restore 失敗:' . $e->getMessage(), ['id' => $id]);
            return response()->json([], 500);
        }

        
    }

    /*
        機能：STEPのもつ子STEPを取得
        引数:親のSTEP id
    */
    public function userSubStepProgress($id)
    {
        try {
            $user = auth()->user();

            // STEP取得（存在チェック込み）
            $step = Step::withTrashed()->findOrFail($id);

            // デフォルトは「削除されていない子STEPのみ」
            $subStepsQuery = $step->subSteps()->orderBy('order_no', 'asc');

            // ユーザーがログインしている場合のみ処理分岐
            if ($user) {
                $challenge = $step->challenges()
                    ->where('user_id', $user->id)
                    ->first();

                //当該STEPオーナーか
                $isOwner = $step->owner_id === $user->id;

                if ($isOwner) {
                    // オーナーは全削除済みを表示
                    $subStepsQuery = $step->subSteps()->withTrashed()->orderBy('order_no', 'asc');
                } elseif ($challenge) {
                    //チャレンジ中ユーザは、チャレンジ開始前に削除された子STEPは除外
                    //(そうしないと、未チャレンジ→チャレンジすると論理削除した子STEPがみえてしまうため)
                    $subStepsQuery = $step->subSteps()
                        ->withTrashed()
                        ->where(function($q) use ($challenge) {
                            $q->whereNull('deleted_at')
                            ->orWhere('deleted_at', '>', $challenge->created_at);
                        })
                        ->orderBy('order_no', 'asc');
                }
            }

            // 子STEPを取得
            $subSteps = $subStepsQuery->get();

            // ログインユーザーの進捗情報を追加
            if ($user) {
                foreach ($subSteps as $subStep) {
                    $userProgress = $subStep->progresses()
                        ->whereHas('challenge', function($q) use ($user) {
                            $q->where('user_id', $user->id);
                        })
                        ->get();

                    $subStep->userProgress = $userProgress;
                }
            }

            return response()->json($subSteps);

        } catch (AuthorizationException $e) {
            Log::error('SubSteps userSubStepProgress 権限エラー:' . $e->getMessage());
            return response()->json([], 403);
        } catch (ModelNotFoundException $e) {
            Log::error('SubSteps userSubStepProgress モデル未発見:' . $e->getMessage());
            return response()->json([], 404);
        } catch (Exception $e) {
            Log::error('SubSteps userSubStepProgress 失敗:' . $e->getMessage(), ['id' => $id]);
            return response()->json([], 500);
        }

    }

    /*
        概要：子STEPの並び順を更新
        引数：
     *      subSteps: 配列形式
     *       [
     *          { "id": 子STEPのID, "no": 並び順の番号 },
     *          { "id": 子STEPのID, "no": 並び順の番号 },
     *          ...
     *       ]
    */
    public function updateOrder(Request $request)
    {
        try {
            //当該子STEP idと順番を$requestから取り出し、当該子STEPを更新
            foreach($request->subSteps as $subStep) {
                SubStep::where('id', $subStep['id'])
                    ->update(['order_no' => $subStep['no']]);
            }
            return response()->json(['msg' => '子STEPの並び順を更新しました']);
        } catch (AuthorizationException $e) {
            Log::error('SubSteps updateOrder 権限エラー:' . $e->getMessage());
            return response()->json([], 403);
        } catch (ModelNotFoundException $e) {
            Log::error('SubSteps updateOrder モデル未発見:' . $e->getMessage());
            return response()->json([], 404);
        } catch (Exception $e) {
            Log::error('SubSteps updateOrder 失敗:' . $e->getMessage(), ['request' => $request->all()]);
            return response()->json([], 500);
        }

    }
}
