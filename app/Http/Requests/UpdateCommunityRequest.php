<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCommunityRequest extends FormRequest
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
            "data.attributes.user_id"=>"required",
            "data.attributes.community_id"=>"required",
            "data.attributes.title"=>"required",
            "data.attributes.description"=>"required",
            "data.attributes.country"=>"required",
            "data.attributes.flag"=>"required",
            "data.attributes.language"=>"required",
            "data.attributes.timezone"=>"required",
            "data.attributes.game_id"=>"required",
            "data.attributes.game_name"=>"required",
            "data.attributes.game_image"=>"required",
        ];
    }
}
