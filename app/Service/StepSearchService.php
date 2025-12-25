<?php

namespace App\Service;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class StepSearchService
{
    //検索結果を加味したSTEPを取得
    public function searchSteps(Request $request, $query)
    {
        //カテゴリ別に検索
        if ($request->filled('categoryId')) {
            $query->where('category_id', $request->categoryId);
        }

        // 達成時間別に検索
        if ($request->filled('selectedEstimatedTime')) {
            switch ($request->selectedEstimatedTime) {
                case 'short':
                    $min = 0;
                    $max = 180;
                    break;
                case 'medium_short':
                    $min = 181;
                    $max = 360;
                    break;
                case 'standard':
                    $min = 361;
                    $max = 720;
                    break;
                case 'long':
                    $min = 721;
                    $max = 1440;
                    break;
                case 'very_long':
                    $min = 1441;
                    $max = null;
                    break;
                default:
                    $min = null;
                    $max = null;
            }

            if (!is_null($min) && !is_null($max)) {
                $query->whereBetween('total_estimated_time', [$min, $max]);
            } elseif (!is_null($min)) {
                // 上限なしのケース（very_long）
                $query->where('total_estimated_time', '>=', $min);
            }
        }

        //文字列検索
        if (!empty($request->keyword)) {
            $keyword = '%' . $request->keyword . '%';
            $query->whereRaw("(step_name LIKE ? OR step_detail LIKE ?)", [$keyword, $keyword]);
        }

        switch ($request->sortId) {
            case 11: // 新しい順
                $query->orderBy('created_at', 'desc');
                break;
            case 12: // 古い順
                $query->orderBy('created_at', 'asc');
                break;
            case 21: // チャレンジ多
                $query->orderBy('challenges_count', 'desc');
                break;
            case 22: // チャレンジ少
                $query->orderBy('challenges_count', 'asc');
                break;
            case 31: // 達成時間長
                $query->orderBy('total_estimated_time', 'desc');
                break;
            case 32: // 達成時間短
                $query->orderBy('total_estimated_time', 'asc');
                break;
            default:
                break;
        }

        $steps = $query->get();
        return $steps;
    
    }
}