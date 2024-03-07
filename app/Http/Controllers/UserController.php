<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Community;
use App\Models\Comment;
use App\Models\JoinCommunity;
use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\RegisterUserRequest;
use App\Http\Requests\UpdateUserNameRequest;
use App\Http\Requests\UpdateUserEmailRequest;
use App\Http\Requests\UpdateUserPasswordRequest;
use App\Http\Requests\DeleteUserRequest;
use App\Http\Resources\UserResource;
use App\Http\Requests\GetUsersRequest;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function checkUser (Request $request) {
        Auth::shouldUse('sanctum');
        $user = Auth::user(); // Usuario del token
        $access = false;
        if ($user->role === 'user') {
            $access = false;
        } else if ($user->role === 'admin') {
            $access = true;
        }
        // return $access;
        return response()->json($access);
    }

    public function login (LoginUserRequest $request) {
        $data = $request->input("data.attributes");
        $email = $data['email'];
        $password = $data['password'];
        $user = User::whereEmail($email)->first();  //Buscamos el user
        if (!$user || !Hash::check($password, $user->password)) {   //Si no existe el user o no es correcta la pass
            return response()->json([
                "success" => false,
                'error' => 'Incorrect user or password'
            ], 200);             
        } else if ($user || Hash::check($password, $user->password)) {
            $device_name = $data["device_name"];    //Obtenemos en device_name para crear el token
            $token = $user->createToken($device_name,['*'])->plainTextToken;    //Creamos el token
            //Return
            // $return = (new UserResource($user))
            // ->additional(["meta" => ["token" => $token, "success" => false]])
            // ->response()
            // ->setStatusCode(200);
            // return $return;
            return response()->json([
                "success" => true,
                "token" => $token,
                "id" => $user->id,
                "email" => $user->email,
                "username" => $user->username,
                "role" => $user->role
            ], 200);
        } else {
            return response()->json([
                "success" => false,
                'error' => 'Incorrect user or password'
            ], 200);    
        }
    }

    public function register (RegisterUserRequest $request) {
        $user = new User ($request->input('data.attributes'));  //Creamos un nuevo user
        $user->save();  //Le guardamos en la BD
        $device_name = ($request->input('data.attributes.device_name'));    //Obtenemos en device_name para crear el token
        $token = $user->createToken($device_name,['*'])->plainTextToken;    //Creamos el token
        //Return
        return response()->json([
            "success" => true,
            "token" => $token,
            "id" => $user->id,
            "email" => $user->email,
            "username" => $user->username,
            "role" => $user->role
        ], 200);
        // $return = (new UserResource($user))
        // ->additional(["meta"=>["token"=>$token]])
        // ->response()
        // ->setStatusCode(201);
        // return $return;
    }

    public function profile (Request $request) {
        Auth::shouldUse('sanctum'); // Indicar a Laravel que utilice el guard 'sanctum'
        $user = Auth::user(); // Usuario del token
        return $user;
    }

    public function updateUsername (UpdateUserNameRequest $request) {
        $data = $request->input("data.attributes");
        $id = $data['id'];
        $username = $data['username'];
        $user = User::whereId($id)->first();  //Buscamos el user
        $user->username = $username;
        if ($user->save()) {
            return response()->json(['message' => 'Name actualizado correctamente','user' => $user], 200);
        } else {
            return response()->json(['error' => 'Error al actualizar el name del user'], 404);
        }
    }

    public function updateUserEmail (UpdateUserEmailRequest $request) {
        $data = $request->input("data.attributes");
        $id = $data['id'];
        $email = $data['email'];
        $user = User::whereId($id)->first();  //Buscamos el user
        $user->email = $email;
        if ($user->save()) {
            return response()->json(['message' => 'Email actualizado correctamente','user' => $user], 200);
        } else {
            return response()->json(['error' => 'Error al actualizar el email del user'], 404);
        }
    }

    public function updateUserPassword (UpdateUserPasswordRequest $request) {
        $data = $request->input("data.attributes");
        // $oldpassword = $data['oldpassword'];
        $userid = $data['id'];
        $newpassword = $data['newpassword'];
        Auth::shouldUse('sanctum'); // Indicar a Laravel que utilice el guard 'sanctum'
        $user = Auth::user(); // Usuario del token
        if ($user->id !== $userid) {
            if ($user->role === 'admin') {
                $userdb = User::whereId($userid)->first();  //Buscamos el user
                $userdb->password = Hash::make($newpassword);
                if ($userdb->save()) {
                    return response()->json(['message' => 'Contraseña actualizada correctamente','user' => $user], 200);
                } else {
                    return response()->json(['error' => 'Error al actualizar la contraseña del user'], 404);
                }
            }
        } else if ($user->id === $userid) {
                            // Verifica que la contraseña actual coincida
                // if (!Hash::check($oldpassword, $user->password)) {
                //     return response()->json(['error' => 'La contraseña actual no es correcta'], 400);
                // }
                // Actualiza la contraseña del usuario
                $user->password = Hash::make($newpassword);
                if ($user->save()) {
                    return response()->json(['message' => 'Contraseña actualizada correctamente','user' => $user], 200);
                } else {
                    return response()->json(['error' => 'Error al actualizar la contraseña del user'], 404);
                }
        } else {
            return response()->json(['error' => 'Error al actualizar la contraseña del user'], 404);
        }
    }

    public function deleteUser (DeleteUserRequest $request) {
        $data = $request->input("data.attributes");
        $userid = $data['id'];
        Auth::shouldUse('sanctum');
        $user = Auth::user(); // Usuario del token
        if ($user->id !== $userid) {
            if ($user->role === 'admin') {
                $userdb = User::whereId($userid)->first();  //Buscamos el user
                if ($userdb) {
                    Comment::where('user_id', $userid)->delete();  //Primero borramos todos los comentarios
                    JoinCommunity::where('user_id', $userid)->delete();  //Segundo borramos los joinCommunities
                    Community::where('user_id', $userid)->delete();  //Segundo borramos llas comunidades
                    $userdb->delete();   //Despues borramos la comunidad
                    return response()->json(['message' => 'User borrado correctamente','user' => $userdb], 200);
                } else {
                    return response()->json(['error' => 'No se encontró ningun user para borrar'], 404);
                }
            }
        } else if ($user->id === $userid) {
            $userdb = User::whereId($userid)->first();  //Buscamos el user
            if ($userdb) {
                Comment::where('user_id', $userid)->delete();  //Primero borramos todos los comentarios
                JoinCommunity::where('user_id', $userid)->delete();  //Segundo borramos los joinCommunities
                Community::where('user_id', $userid)->delete();  //Segundo borramos llas comunidades
                $userdb->delete();   //Despues borramos la comunidad
                return response()->json(['message' => 'User borrado correctamente','user' => $userdb], 200);
            } else {
                return response()->json(['error' => 'No se encontró ningun user para borrar'], 404);
            }
        }
        // return $user;
    }

    public function getUsers (GetUsersRequest $request) {
        Auth::shouldUse('sanctum');
        $user = Auth::user(); // Usuario del 
        $perPage = $request->input('limit', 12); // Obtenemos el parámetro "limit" de la solicitud, o 12 por defecto
        $page = $request->input('page'); // Obtener el parámetro "page" de la solicitud, o 1 por defecto
        $response;
        if ($user->role === 'admin') {
            $users = User::paginate($perPage, ['*'], 'page', $page);
            // $allUsers = User::all();
            $response = $users;
            return response()->json($response);
        } else if ($user->role === 'user') {
            $response = null;
        }
        // return $access;
        return $response;
    }

}
