<?php

namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;


class UserRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'sometimes|required|min:6',
        ];
    }
}
