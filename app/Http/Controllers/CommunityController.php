<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Community;
use App\Models\Comment;
use App\Models\JoinCommunity;
use App\Http\Requests\CreateCommunityRequest;
use App\Http\Requests\JoinCommunityRequest;
use App\Http\Requests\UpdateCommunityRequest;
use App\Http\Requests\LeaveCommunityRequest;
use App\Http\Requests\DeleteCommunityRequest;

class CommunityController extends Controller
{
    // Función para cuando no haya parámetros para getCoomunities (por defecto)
    private function initCommunities () {
        // Popular
        $communities = Community::orderBy('num_persons', 'desc')->get();
        if ($communities) {
            return $communities;
        } else {
            return response()->json([
                'error' => [
                    'status' => 500,
                    'title' => 'Database Error',
                    'details' => 'An error occurred while processing your request.'
                ]
            ], 500);
        }
    }

    public function getCommunities (Request $request) {
        try {
            if ($request->input('l') && $request->input('o')) {
                $request->validate([
                    'l' => 'required|string',
                    'o' => 'required|string',
                ]);
                if ($request->input('o') != 'asc' || $request->input('o') != 'desc') {
                    return $this->initCommunities();
                }
                // if ($request->input('l') != '?') {  //Comprobar si es un idioma (pasar un json)
                //     return $this->initCommunities();
                // }
                $language = $request->input('l');
                $order = $request->input('o');
                $communities = Community::where('language', $language)->orderBy('num_persons', $order)->get();
                if ($communities) {
                    return $communities;
                } else {
                    // return response()->json([
                    //     'error' => [
                    //         'status' => 500,
                    //         'title' => 'Database Error',
                    //         'details' => 'An error occurred while processing your request.'
                    //     ]
                    // ], 500);
                    return $this->initCommunities();
                }
            } else if ($request->input('l') && !$request->input('o')) {
                $request->validate([
                    'l' => 'required|string'
                ]);
                // if ($request->input('l') != '?') {  //Comprobar si es un idioma (pasar un json)
                //     return $this->initCommunities();
                // }
                $language = $request->input('l');
                $communities = Community::where('language', $language)->orderBy('num_persons', 'desc')->get();
                if ($communities) {
                    return $communities;
                } else {
                    // return response()->json([
                    //     'error' => [
                    //         'status' => 500,
                    //         'title' => 'Database Error',
                    //         'details' => 'An error occurred while processing your request.'
                    //     ]
                    // ], 500);
                    return $this->initCommunities();
                }
            }  else if (!$request->input('l') && $request->input('o')) {
                $request->validate([
                    'o' => 'required|string'
                ]);
                if ($request->input('o') != 'asc' || $request->input('o') != 'desc') {
                    return $this->initCommunities();
                }
                $order = $request->input('o');
                $communities = Community::orderBy('num_persons', $order)->get();
                if ($communities) {
                    return $communities;
                } else {
                    // return response()->json([
                    //     'error' => [
                    //         'status' => 500,
                    //         'title' => 'Database Error',
                    //         'details' => 'An error occurred while processing your request.'
                    //     ]
                    // ], 500);
                    return $this->initCommunities();
                }
            } else {
                return $this->initCommunities();
            }
        } catch (ValidationException $e) {
            // $errors = $e->validator->errors()->toArray()->json(['errors' => $errors], 422);
            // return $errors;
            return $this->initCommunities();
        }
    }

    public function getCommunity (Request $request) {
        // $data = $request->input("data.attributes");
        // $communityId = $data['community_id'];
        $communityId = $request->query('community_id');
        $community = Community::whereId($communityId)->first();  //Buscamos la comunidad
        return $community;
    }

    public function getMyCommunities (Request $request) {
        Auth::shouldUse('sanctum'); // Indicar a Laravel que utilice el guard 'sanctum'
        $user = Auth::user(); // Usuario del token
        $id = $user['id'];
        $communities = Community::where('user_id', $id)->get(); //Obtenemos las comunidades
        return $communities;
    }

    public function createCommunity (CreateCommunityRequest $request) {
        $data = $request->input("data.attributes");
        $community = new Community($data);
        $community->save();
        return $community;
    }

    public function joinCommunity (JoinCommunityRequest $request) {
        $data = $request->input("data.attributes");
        $joinCommunity = new JoinCommunity($data);
        $joinCommunity->save();
        // Incrementar num_persons de la tabla communities
        $communityId = $data['community_id']; // Obtener el ID de la comunidad
        $community = Community::whereId($communityId)->first();  //Buscamos la comunidad
        $community->increment('num_persons'); // Incrementar el campo 'num_members' en 1
        return $community;
    }

    public function leaveCommunity (LeaveCommunityRequest $request) {
        $data = $request->input("data.attributes");
        $user_id = $data['user_id'];
        $community_id = $data['community_id'];
        $joinCommunity = JoinCommunity::where('user_id', $user_id)
        ->where('community_id', $community_id)
        ->first();
        if ($joinCommunity) {
            $joinCommunity->delete();
            // Decrementar num_persons de la tabla communities
            $community = Community::whereId($community_id)->first();  //Buscamos la comunidad
            $community->decrement('num_persons');
            return response()->json(['message' => 'Comunidad dejada correctamente','joinComunity' => $community], 200);
        } else {
            return response()->json(['error' => 'No se encontró ningun user-comunidad para dejar'], 404);
        }
    }

    public function updateCommunity (UpdateCommunityRequest $request) {
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

    public function deleteCommunity (DeleteCommunityRequest $request) {
        $data = $request->input("data.attributes");
        $id = $data['id'];
        $community = Community::whereId($id)->first();  //Buscamos la comunidad
        if ($community) {
            Comment::where('community_id', $id)->delete();  //Primero borramos todos los comentarios
            JoinCommunity::where('community_id', $id)->delete();  //Segundo borramos los joinCommunities
            $community->delete();   //Despues borramos la comunidad
            return response()->json(['message' => 'Comunidad borrada correctamente','communidad' => $community], 200);
        } else {
            return response()->json(['error' => 'No se encontró ninguna comunidad para borrar'], 404);
        }
        return $community;
    }
}
