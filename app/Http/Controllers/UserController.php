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
use App\Http\Requests\UpdateUserRoleRequest;
use App\Http\Requests\DeleteUserRequest;
use App\Http\Resources\UserResource;
use App\Http\Requests\GetUsersRequest;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    //Check user
    public function checkUser (Request $request) {
        Auth::shouldUse('sanctum');
        $user = Auth::user(); // Usuario del token
        $access = false;
        if ($user->role === 'user') {
            $access = false;
        } else if ($user->role === 'admin') {
            $access = true;
        }
        return response()->json($access);
    }

    //Login
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

    //Register
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
    }

    //Profile
    public function profile (Request $request) {
        Auth::shouldUse('sanctum'); // Indicar a Laravel que utilice el guard 'sanctum'
        $user = Auth::user(); // Usuario del token
        return $user;
    }

    //Update Username
    public function updateUsername (UpdateUserNameRequest $request) {
        $data = $request->input("data.attributes");
        $id = $data['id'];
        $username = $data['username'];
        $user = User::whereId($id)->first();
        $user->username = $username;
        if ($user->save()) {
            return response()->json([
                "success" => true,
                "message" => 'Usermaname actualizado con exito'
            ], 200);
        } else {
            return response()->json(['error' => 'Error al actualizar el name del user'], 404);
        }
    }

    //Update Email
    public function updateUserEmail (UpdateUserEmailRequest $request) {
        $data = $request->input("data.attributes");
        $id = $data['id'];
        $email = $data['email'];
        $user = User::whereId($id)->first();
        $user->email = $email;
        if ($user->save()) {
            return response()->json([
                "success" => true,
                "message" => 'Email actualizado con exito'
            ], 200);
        } else {
            return response()->json(['error' => 'Error al actualizar el email del user'], 404);
        }
    }

    //Update Password
    public function updateUserPassword (UpdateUserPasswordRequest $request) {
        $data = $request->input("data.attributes");
        $userid = $data['id'];
        $newpassword = $data['newpassword'];
        Auth::shouldUse('sanctum');
        $user = Auth::user();
        if ($user->id !== $userid) {
            if ($user->role === 'admin') {
                $userdb = User::whereId($userid)->first();
                $userdb->password = Hash::make($newpassword);
                if ($userdb->save()) {
                    return response()->json([
                        "success" => true,
                        "message" => 'Pass actualizado con exito'
                    ],200);
                } else {
                    return response()->json([
                        "success" => false,
                        'error' => 'Error al actualizar la contraseña del user 1'
                    ],404);
                }
            }
        } else if ($user->id === $userid) {
            $userpassword = $data['user_password'];
            $userdb = User::whereId($userid)->first();  //Buscamos el user
            if (!Hash::check($userpassword, $userdb->password)) {
                return response()->json([
                    'success'=> false,
                    'error' => 'Error al actualizar la contraseña del user 2'
                ],404);
            }
            // Actualiza la contraseña del usuario
            $user->password = Hash::make($newpassword);
            if ($user->save()) {
                return response()->json([
                    'success'=> true,
                    'message' => 'Contraseña actualizada correctamente',
                ],200);
            } else {
                return response()->json([
                    'success'=> false,
                    'error' => 'Error al actualizar la contraseña del user 3'
                ],404);
            }
        } else {
            return response()->json([
                'success'=> false,
                'error' => 'Error al actualizar la contraseña del user 4'
            ],404);
        }
    }

    //Update Role
    public function updateUserRole (UpdateUserRoleRequest $request) {
        $data = $request->input("data.attributes");
        $userid = $data['id'];
        $role = $data['role'];
        Auth::shouldUse('sanctum');
        $user = Auth::user();
        if ($user->role === 'admin') {
            $userdb = User::whereId($userid)->first();
            $userdb->role = $role;
            if ($userdb->save()) {
                return response()->json([
                    "success" => true,
                    "message" => 'Role actualizado con exito'
                ],200);
            } else {
                return response()->json([
                    "success" => false,
                    'error' => 'Error al actualizar el role'
                ],404);
            }
        } else {
            return response()->json([
                'success'=> false,
                'error' => 'Error al actualizar el role'
            ],404);
        }
        return $role;
    }

    //Delete User
    public function deleteUser (DeleteUserRequest $request) {
        $data = $request->input("data.attributes");
        $userid = $data['id'];
        Auth::shouldUse('sanctum');
        $user = Auth::user();
        if ($user->id !== $userid) {
            if ($user->role === 'admin') {
                $userdb = User::whereId($userid)->first();
                if ($userdb) {
                    Comment::where('user_id', $userid)->delete();  //Primero borramos los comentarios
                    JoinCommunity::where('user_id', $userid)->delete();  //Segundo borramos los joinCommunities
                    JoinCommunity::where('user_community_id', $userid)->delete();  //Tercero borramos los joinCommunities de otros users
                    Community::where('user_id', $userid)->delete();  //Cuarto borramos las comunidades
                    $userdb->delete();   //Despues borramos la comunidad
                    return response()->json([
                        'success'=> true,
                        'message' => 'User delete correctamente',
                        'data'=> $userdb
                    ],200);
                } else {
                    return response()->json([
                        'success'=> false,
                        'error' => 'Error al delete user'
                    ],404);
                }
            }
        } else if ($user->id === $userid) {
            $userdb = User::whereId($userid)->first();
            if ($userdb) {
                $userpassword = $data['password'];
                if (Hash::check($userpassword, $userdb->password)) {
                    Comment::where('user_id', $userid)->delete();  //Primero borramos los comentarios
                    JoinCommunity::where('user_id', $userid)->delete();  //Segundo borramos los joinCommunities
                    JoinCommunity::where('user_community_id', $userid)->delete();  //Tercero borramos los joinCommunities de otros users
                    Community::where('user_id', $userid)->delete();  //Cuarto borramos las comunidades
                    $userdb->delete();   //Despues borramos la comunidad
                    return response()->json([
                        'success'=> true,
                        'message' => 'User delete correctamente',
                        'data'=> $userdb
                    ],200);
                } else {
                    return response()->json([
                        'success'=> false,
                        'error' => 'Incorrect password'
                    ],404);
                }
            } else {
                return response()->json([
                    'success'=> false,
                    'error' => 'Error al delete user'
                ],404);
            }
        }
    }

    //Get Users
    public function getUsers (GetUsersRequest $request) {
        Auth::shouldUse('sanctum');
        $user = Auth::user();
        //Consulta por paginas
        $perPage = $request->input('limit', 12); // Obtenemos el parámetro "limit" de la solicitud, o 12 por defecto
        $page = $request->input('page'); // Obtener el parámetro "page"
        $response;
        if ($user->role === 'admin') {
            $users = User::paginate($perPage, ['*'], 'page', $page);
            // $allUsers = User::all();
            $response = $users;
            return response()->json($response);
        } else if ($user->role === 'user') {
            $response = null;
        }
        return $response;
    }

    //Get Users Search
    public function getSearchUsers (Request $request) {
        $search = $request->input('search');
        $order = $request->input('order');
        $role = $request->input('role');
    
        $query = User::query();
    
        if ($search !== null && $search !== '') {
            $query->where(function($q) use ($search) {
                $q->where('username', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%");
            });
        }

        if ($role !== null && $role !== '' && $role != 'all') {
            $query->where('role', $role);
        }
    
        switch ($order) {
            case 'usernamedesc':
                $query->orderBy('username', 'DESC');
                break;
            case 'usernameasc':
                $query->orderBy('username', 'ASC');
                break;
            case 'emaildesc':
                $query->orderBy('email', 'DESC');
                break;
            case 'emailasc':
                $query->orderBy('email', 'ASC');
                break;
            case 'roledesc':
                $query->orderBy('role', 'DESC');
                break;
            case 'roleasc':
                $query->orderBy('role', 'ASC');
                break;
            case 'datedesc':
                $query->orderBy('created_at', 'DESC');
                break;
            case 'dateasc':
                $query->orderBy('created_at', 'ASC');
                break;
            default:
                break;
        }
    
        $users = $query->get();
        
        return response()->json($users);
    }
}
