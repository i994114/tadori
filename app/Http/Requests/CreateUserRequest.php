<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateUserRequest extends FormRequest
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
            'email' => ['required', 'email', 'max:255', 'unique:users,email', 'regex:/^[A-Za-z0-9._%+-@]{6,}$/'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'agreedPrivacy' => ['accepted'],
            'agreedTerms' => ['accepted'],
        ];
    }
}
