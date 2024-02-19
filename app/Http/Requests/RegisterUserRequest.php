<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // 'data.attributes.username'=>'required|unique:users,username',
            // 'data.attributes.email' => 'required|email|unique:users,email',
            // 'data.attributes.password' => 'required|min:8|max:30|confirmed',
            // 'data.attributes.device_name'=>'required'
            //
        ];
    }
}
