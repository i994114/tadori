<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EditUserRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:25'],
            'user_profile' => ['nullable', 'string','max:1000'],
            'user_img' => ['image', 'mimes:jpeg,png,gif', 'max:2097152'],    //php.iniのupload_max_filesizeにあわせて2Mとする

        ];
    }
}
