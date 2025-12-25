<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateSubStepRequest extends FormRequest
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
        return [
            'sub_step_name' => ['required', 'string', 'max:50'],
            'sub_step_detail' => ['max:2500'],
            'order_no' => ['min:0', 'max:20'],
            'sub_step_img' => ['image', 'mimes:jpeg,png,gif', 'max:2097152'],
            'estimated_time' => 'nullable|integer|min:0|max:1440',   //最大1日とする
        ];
    }
}
