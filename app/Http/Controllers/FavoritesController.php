<?php

namespace App\Http\Controllers;

use App\User;
use App\Favorite;
use Illuminate\Http\Request;
use App\Service\StepSearchService;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\CreateFavoriteRequest;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class FavoritesController extends Controller
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
    public function store(CreateFavoriteRequest $request)
    {
        try {
            $this->authorize('create', [Favorite::class, $request->all()]);

            $favorite = new Favorite;

            $favorite->step_id = $request->step_id;
            $favorite->user_id = $request->user_id;

            $favorite->save();
            
            return response()->json($favorite);
        } catch (AuthorizationException $e) {
            Log::error('Favorites store失敗(権限エラー): ' . $e->getMessage(), ['request' => $request->all()]);
            return response()->json([], 403);
        } catch (ModelNotFoundException $e) {
            Log::error('Favorites store失敗(モデル未発見): ' . $e->getMessage(), ['request' => $request->all()]);
            return response()->json([], 404);
        } catch (Exception $e) {
            Log::error('Favorites store失敗: ' . $e->getMessage(), ['request' => $request->all()]);
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
    public function destroy(Favorite $favorite)
    {
        try {
            $this->authorize('forceDelete', $favorite);

            if ($favorite) {
                $favorite->forceDelete();
                return response()->json(['msg' => 'お気に入り登録解除しました']);
            } else {
                return response()->json(['error' => '当該データがありません'], 404);
            }
        } catch (AuthorizationException $e) {
            Log::error('Favorites destroy失敗(権限エラー): ' . $e->getMessage(), ['favorite' => $favorite]);
            return response()->json([], 403);
        } catch (ModelNotFoundException $e) {
            Log::error('Favorites destroy失敗(モデル未発見): ' . $e->getMessage(), ['favorite' => $favorite]);
            return response()->json([], 404);
        } catch (Exception $e) {
            Log::error('Favorites destroy失敗: ' . $e->getMessage(), ['favorite' => $favorite]);
            return response()->json([], 500);
        }

    }

    //当該ユーザのお気に入りしたSTEP情報を取得
    public function userFavorites(Request $request) {
        $user = auth()->user();

        $query = $user->favorites()
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

    //当該ユーザのお気に入りを全削除
    public function destroyMyFavorites($id)
    {
        try {
            $user = User::findOrFail($id);
            $user->favorites()->detach();
        } catch (AuthorizationException $e) {
            Log::error('Favorites destroyMyFavorites失敗(権限エラー): ' . $e->getMessage(), ['id' => $id]);
            return response()->json([], 403);
        } catch (ModelNotFoundException $e) {
            Log::error('Favorites destroyMyFavorites失敗(モデル未発見): ' . $e->getMessage(), ['id' => $id]);
            return response()->json([], 404);
        } catch (Exception $e) {
            Log::error('Favorites destroyMyFavorites失敗: ' . $e->getMessage(), ['id' => $id]);
            return response()->json([], 500);
        }

    }
}
