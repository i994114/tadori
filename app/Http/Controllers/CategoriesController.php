<?php

namespace App\Http\Controllers;

use App\Category;
use App\Constants\UserType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\EditCategoryRequest;
use App\Http\Requests\CreateCategoryRequest;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CategoriesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //論理削除したものを除いたカテゴリ一覧を取得
        $categories = Category::whereNull('deleted_at')->get();
        return response()->json($categories);
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
    public function store(CreateCategoryRequest $request)
    {
        try {
            $category = new Category();
            $category->category_name = $request->newCategoryName;
            if(!$category->save()) {
                return response()->json([], 500);
            }

            return response()->json(['msg' => 'カテゴリを追加しました']);
        } catch (AuthorizationException $e) {
            Log::error('Categories store失敗(権限エラー): ' . $e->getMessage(), ['request' => $request->all()]);
            return response()->json([], 403);
        } catch (\Exception $e) {
            Log::error('Categories store失敗: ' . $e->getMessage(), ['request' => $request->all()]);
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
    public function update(EditCategoryRequest $request, $id)
    {
        try {
            $category = Category::findOrFail($id);
            $category->category_name = $request->editCategoryName;
            $category->save();

            return response()->json(['msg' => 'カテゴリ名を変更しました']);
        } catch (AuthorizationException $e) {
            Log::error('Categories update失敗(権限エラー): ' . $e->getMessage(), ['request' => $request->all()]);
            return response()->json([], 403);
        } catch (ModelNotFoundException $e) {
            Log::error('Categories update失敗(モデル未発見): ' . $e->getMessage(), ['request' => $request->all()]);
            return response()->json([], 404);
        } catch (Exception $e) {
            Log::error('Categories update失敗: ' . $e->getMessage(), ['request' => $request->all()]);
            return response()->json([], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $category = Category::findOrFail($id);
            $category->delete();

            return response()->json(['msg' => 'カテゴリを削除しました']);
        } catch (AuthorizationException $e) {
            Log::error('Categories destroy失敗(権限エラー): ' . $e->getMessage(), ['id' => $id]);
            return response()->json([], 403);
        } catch (ModelNotFoundException $e) {
            Log::error('Categories destroy失敗(モデル未発見): ' . $e->getMessage(), ['id' => $id]);
            return response()->json([], 404);
        } catch (Exception $e) {
            Log::error('Categories destroy失敗: ' . $e->getMessage(), ['id' => $id]);
            return response()->json([], 500);
        }

    }

    //削除したカテゴリーを復活
    public function restore($id)
    {
        try {
            $category = Category::onlyTrashed()->findOrFail($id);
            $category->restore();
            return response()->json(['msg' => '削除されたカテゴリを復活させました']);
        } catch (AuthorizationException $e) {
            Log::error('Categories restore失敗(権限エラー): ' . $e->getMessage(), ['id' => $id]);
            return response()->json([], 403);
        } catch (ModelNotFoundException $e) {
            Log::error('Categories restore失敗(モデル未発見): ' . $e->getMessage(), ['id' => $id]);
            return response()->json([], 404);
        } catch (Exception $e) {
            Log::error('Categories restore失敗: ' . $e->getMessage(), ['id' => $id]);
            return response()->json([], 500);
        }

    }

    //論理削除も含めたカテゴリ一覧
    public function indexAll()
    {

        //ログインユーザが管理者の場合は論理削除カテゴリも含めて取得
        $categories = Category::withTrashed()->get();
        
        return response()->json($categories);        
    }
}
