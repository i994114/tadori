<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Http\FormRequest;

class CreateStepRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        //post, putルール
        return [
            'step_name' => ['required', 'string', 'max:50'],
            'step_detail' => ['max:2500'],
            'category_id' => ['required', 'exists:categories,id'],
            'step_img' => ['image', 'mimes:jpeg,png,gif', 'max:2097152'],
            'total_estimated_time' => 'nullable|integer|min:0|max:28800',   // 子STEPは最大20個、各子STEPの最大時間は1440分 → 1440 × 20 = 28800分（STEP全体の上限）
        ];

    }
}
