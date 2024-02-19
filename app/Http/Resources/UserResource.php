<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id"=>(string)$this->id,
            "type"=>"User",
            "atributes"=>[
                "id"=>$this->id,
                "name"=>$this->username,
                "email"=>$this->email
            ]
        ];
    }

    // public function with(Request $request) {
    //     return ["jsonapi"=>["version"=>"1.0"]];
    // }
}
