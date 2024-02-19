<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\RegisterUserRequest;

class LoginController extends Controller
{
    // public function login (LoginUserRequest $request) {

    // }

    // public function hola ($request) {
    //     info("HOLA");
    //     $data = new User ($request->input('data.attributes'));
    //     //1
    //     // $user = User::create([
    //     //     'username' => $data['username'],
    //     //     'email' => $data['email'],
    //     //     'password' => bcrypt($data['password']),
    //     // ]);
    //     //2
    //     // $user = new User();
    //     // $user->username = $data['username'];
    //     // $user->email = $data['email'];
    //     // $user->password = bcrypt($data['password']);

    //     // $user->save(); // Guardar el usuario en la base de datos
    //     // $device_name = ($request->input('data.attributes.device_name'));
    //     // $token = $user->createToken($device_name,['*'])->plainTextToken;
    //     // info('LoginController@register con token -$token-');

    //     // return $user;
    //     return $data;
    // }

    public function register (RegisterUserRequest $request) {
        return $request->input('data.attributes');
    }
}
