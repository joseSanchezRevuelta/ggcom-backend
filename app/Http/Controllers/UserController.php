<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\RegisterUserRequest;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function login (LoginUserRequest $request) {
        $data = $request->input("data.attributes");
        $email = $data['email'];
        $password = $data['password'];
        $user = User::whereEmail($email)->first();  //Buscamos el user
        if (!$user || !Hash::check($password, $user->password)) {   //Si no existe el user o no es correcta la pass
            return response()->json([
                "errors" => [
                    "status" => 422,
                    "title" => "Resource not found",
                    "details" => "Incorrect user or password"
                ]
            ], 422);
        }
        $device_name = $data["device_name"];    //Obtenemos en device_name para crear el token
        $token = $user->createToken($device_name,['*'])->plainTextToken;    //Creamos el token
        //Return
        $return = (new UserResource($user))
        ->additional(["meta"=>["token"=>$token]])
        ->response()
        ->setStatusCode(201);
        return $return;
    }

    public function register (RegisterUserRequest $request) {
        $user = new User ($request->input('data.attributes'));  //Creamos un nuevo user
        $user->save();  //Le guardamos en la BD
        $device_name = ($request->input('data.attributes.device_name'));    //Obtenemos en device_name para crear el token
        $token = $user->createToken($device_name,['*'])->plainTextToken;    //Creamos el token
        //Return
        $return = (new UserResource($user))
        ->additional(["meta"=>["token"=>$token]])
        ->response()
        ->setStatusCode(201);
        return $return;
    }

    public function updateUser (UpdateUserRequest $request) {
        $data = $request->input("data.attributes");
        $id = $data['id'];
        $title = $data['title'];
        $language = $data['language'];
        $community = Community::whereId($id)->first();  //Buscamos la comunidad
        if ($community) {
            $community->title = $title;
            $community->language = $language;
            $community->save();
            return response()->json(['message' => 'Comunidad actualizada correctamente','communidad' => $community], 200);
        } else {
            return response()->json(['error' => 'No se encontró ninguna comunidad para actualizar'], 404);
        }
    }
}
