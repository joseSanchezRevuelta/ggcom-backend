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

    // getCommunities
    public function getCommunities (Request $request) {
        // Definir la cantidad de elementos por página, puedes ajustarlo según tus necesidades
        $perPage = $request->input('limit', 12); // Obtenemos el parámetro "limit" de la solicitud, o 12 por defecto
        $page = $request->input('page'); // Obtener el parámetro "page" de la solicitud, o 1 por defecto

        $communities = Community::orderBy('num_persons', 'desc')->paginate($perPage, ['*'], 'page', $page);
        // $communities = Community::orderBy('num_persons', 'desc')->get();
        // if ($communities->isEmpty()) {
        //     return response()->json([
        //         'error' => [
        //             'status' => 404,
        //             'title' => 'No communities found',
        //             'details' => 'There are no communities available.'
        //         ]
        //     ], 404);
        // }

        // Devolver comunidades paginadas
        return $communities;
    }

    // Función para cuando no haya parámetros para getCoomunities (por defecto)
    // private function initCommunities () {
    //     // Popular
    //     $communities = Community::orderBy('num_persons', 'desc')->get();
    //     if ($communities) {
    //         return $communities;
    //     } else {
    //         return response()->json([
    //             'error' => [
    //                 'status' => 500,
    //                 'title' => 'Database Error',
    //                 'details' => 'An error occurred while processing your request.'
    //             ]
    //         ], 500);
    //     }
    // }

    // public function getCommunities (Request $request) {
    //     try {
    //         if ($request->input('l') && $request->input('o')) {
    //             $request->validate([
    //                 'l' => 'required|string',
    //                 'o' => 'required|string',
    //             ]);
    //             if ($request->input('o') != 'asc' || $request->input('o') != 'desc') {
    //                 return $this->initCommunities();
    //             }
    //             // if ($request->input('l') != '?') {  //Comprobar si es un idioma (pasar un json)
    //             //     return $this->initCommunities();
    //             // }
    //             $language = $request->input('l');
    //             $order = $request->input('o');
    //             $communities = Community::where('language', $language)->orderBy('num_persons', $order)->get();
    //             if ($communities) {
    //                 return $communities;
    //             } else {
    //                 // return response()->json([
    //                 //     'error' => [
    //                 //         'status' => 500,
    //                 //         'title' => 'Database Error',
    //                 //         'details' => 'An error occurred while processing your request.'
    //                 //     ]
    //                 // ], 500);
    //                 return $this->initCommunities();
    //             }
    //         } else if ($request->input('l') && !$request->input('o')) {
    //             $request->validate([
    //                 'l' => 'required|string'
    //             ]);
    //             // if ($request->input('l') != '?') {  //Comprobar si es un idioma (pasar un json)
    //             //     return $this->initCommunities();
    //             // }
    //             $language = $request->input('l');
    //             $communities = Community::where('language', $language)->orderBy('num_persons', 'desc')->get();
    //             if ($communities) {
    //                 return $communities;
    //             } else {
    //                 // return response()->json([
    //                 //     'error' => [
    //                 //         'status' => 500,
    //                 //         'title' => 'Database Error',
    //                 //         'details' => 'An error occurred while processing your request.'
    //                 //     ]
    //                 // ], 500);
    //                 return $this->initCommunities();
    //             }
    //         }  else if (!$request->input('l') && $request->input('o')) {
    //             $request->validate([
    //                 'o' => 'required|string'
    //             ]);
    //             if ($request->input('o') != 'asc' || $request->input('o') != 'desc') {
    //                 return $this->initCommunities();
    //             }
    //             $order = $request->input('o');
    //             $communities = Community::orderBy('num_persons', $order)->get();
    //             if ($communities) {
    //                 return $communities;
    //             } else {
    //                 // return response()->json([
    //                 //     'error' => [
    //                 //         'status' => 500,
    //                 //         'title' => 'Database Error',
    //                 //         'details' => 'An error occurred while processing your request.'
    //                 //     ]
    //                 // ], 500);
    //                 return $this->initCommunities();
    //             }
    //         } else {
    //             return $this->initCommunities();
    //         }
    //     } catch (ValidationException $e) {
    //         // $errors = $e->validator->errors()->toArray()->json(['errors' => $errors], 422);
    //         // return $errors;
    //         return $this->initCommunities();
    //     }
    // }

    public function getSearchCommunities (Request $request) {
        $search = $request->input('search');
        $game_id = $request->input('game_id');
        $country = $request->input('country');
        $language = $request->input('language');
        $timezone = $request->input('timezone');
        $order = $request->input('order');

        $query = Community::query();

        if ($search !== null && $search !== '') {
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%$search%")
                  ->orWhere('description', 'like', "%$search%")
                  ->orWhere('game_name', 'like', "%$search%");
            });
        }

        if ($game_id !== null && $game_id !== '') {
            $query->where('game_id', $game_id);
        }

        if ($country !== null && $country !== 'all') {
            $query->where('country', $country);
        }

        if ($language !== null && $language !== 'all') {
            $query->where('language', $language);
        }

        if ($timezone !== null && $timezone !== 'all') {
            $query->where('language', $timezone);
        }

        switch ($order) {
            case 'mostpopular':
                $query->orderBy('num_comments', 'DESC');
                break;
            case 'lesspopular':
                $query->orderBy('num_comments', 'ASC');
                break;
            case 'morepeople':
                $query->orderBy('num_persons', 'DESC');
                break;
            case 'lesspeople':
                $query->orderBy('num_persons', 'ASC');
                break;
            default:
                break;
        }

        $communities = $query->get();

        return response()->json($communities);
    }

    public function getCommunity (Request $request) {
        // $data = $request->input("data.attributes");
        // $communityId = $data['community_id'];
        $communityId = $request->query('community_id');
        $community = Community::whereId($communityId)->first();  //Buscamos la comunidad
        return $community;
    }

    public function getEditCommunity (Request $request) {
        // $data = $request->input("data.attributes");
        // $communityId = $data['community_id'];
        $communityId = $request->query('community_id');
        $community = Community::whereId($communityId)->first();  //Buscamos la comunidad
        Auth::shouldUse('sanctum'); // Indicar a Laravel que utilice el guard 'sanctum'
        $user = Auth::user(); // Usuario del token
        if ($user->id == $community->user_id) {
            return $community;
        } else if ($user->id != $community->user_id && $user->role == 'admin') {
            return $community;
        }
    }

    public function getMyJoinCommunities (Request $request) {
        Auth::shouldUse('sanctum'); // Indicar a Laravel que utilice el guard 'sanctum'
        $user = Auth::user(); // Usuario del token
        $id = $user['id'];
        $joincommunities = JoinCommunity::where('user_id', $id)->get(); //Obtenemos las comunidades
        $arrayIds = [];
        $joincommunities->map(function($item) use (&$arrayIds) {
            $arrayIds[] = $item['community_id'];
        });
        $communities = Community::whereIn('id', $arrayIds)->get();
        return $communities;
    }

    public function getCreatedCommunities (Request $request) {
        $userid = $request->query('user_id');
        Auth::shouldUse('sanctum'); // Indicar a Laravel que utilice el guard 'sanctum'
        $user = Auth::user(); // Usuario del token
        if ($user->id != $userid) {
            if ($user->role === 'admin') {
                $communities = Community::where('user_id', $userid)->get(); //Obtenemos las comunidades
                return $communities;
            }
        } else if ($user->id == $userid) {
            $communities = Community::where('user_id', $userid)->get(); //Obtenemos las comunidades
            return $communities;
        }
    }

    public function getJoinCommunity (Request $request) {
        $communityId = $request->input('community_id');
        Auth::shouldUse('sanctum'); // Indicar a Laravel que utilice el guard 'sanctum'
        $user = Auth::user(); // Usuario del tokens
        $id = $user['id'];
        $joincommunity = JoinCommunity::where('user_id', $id)->where('community_id', $communityId)->get();
        return $joincommunity;
    }

    public function getJoinCommunities (Request $request) {
        Auth::shouldUse('sanctum'); // Indicar a Laravel que utilice el guard 'sanctum'
        $user = Auth::user(); // Usuario del token
        $id = $user['id'];
        $joincommunities = JoinCommunity::where('user_id', $id)->get(); //Obtenemos las comunidades
        return $joincommunities;
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
        $userid = $data['user_id'];
        $communityid = $data['community_id'];
        $title = $data['title'];
        $description = $data['description'];
        $country = $data['country'];
        $flag = $data['flag'];
        $language = $data['language'];
        $timezone = $data['timezone'];
        $game_id = $data['game_id'];
        $game_name = $data['game_name'];
        $game_image = $data['game_image'];
        $community = Community::whereId($communityid)->first();  //Buscamos la comunidad
        if ($community) {
            Auth::shouldUse('sanctum'); // Indicar a Laravel que utilice el guard 'sanctum'
            $user = Auth::user(); // Usuario del token
            if ($user->id != $userid) {
                if ($user->role === 'admin') {
                    $community->title = $title;
                    $community->description = $description;
                    $community->country = $country;
                    $community->flag = $flag;
                    $community->language = $language;
                    $community->timezone = $timezone;
                    $community->game_id = $game_id;
                    $community->game_name = $game_name;
                    $community->game_name = $game_name;
                    $community->game_image = $game_image;
                    $community->save();
                    return response()->json(['message' => 'Comunidad actualizada correctamente','communidad' => $community], 200);
                }
            } else if ($user->id == $userid) {
                $community->title = $title;
                $community->description = $description;
                $community->country = $country;
                $community->flag = $flag;
                $community->language = $language;
                $community->timezone = $timezone;
                $community->game_id = $game_id;
                $community->game_name = $game_name;
                $community->game_name = $game_name;
                $community->game_image = $game_image;
                $community->save();
                return response()->json(['message' => 'Comunidad actualizada correctamente','communidad' => $community], 200);
            }
        }
        return response()->json(['error' => 'No se encontró ninguna comunidad para actualizar'], 404);
    }

    public function updateTitleCommunity (Request $request) {
        $data = $request->input("data.attributes");
        $communityId = $data['community_id'];
        $title = $data['title_new'];
        $community = Community::whereId($communityId)->first();  //Buscamos la comunidad
        Auth::shouldUse('sanctum'); // Indicar a Laravel que utilice el guard 'sanctum'
        $user = Auth::user(); // Usuario del tokens
        if ($user->id !== $community->user_id) {
            $community->title = $title;
            $community->save();
            return $community;
        } else if ($user->role === 'admin') {
            $community->title = $title;
            $community->save();
            return $community;
        }
    }

    public function updateDescriptionCommunity (Request $request) {
        $data = $request->input("data.attributes");
        $communityId = $data['community_id'];
        $description = $data['description_new'];
        $community = Community::whereId($communityId)->first();  //Buscamos la comunidad
        Auth::shouldUse('sanctum'); // Indicar a Laravel que utilice el guard 'sanctum'
        $user = Auth::user(); // Usuario del tokens
        if ($user->id !== $community->user_id) {
            $community->description = $description;
            $community->save();
            return $community;
        } else if ($user->role === 'admin') {
            $community->description = $description;
            $community->save();
            return $community;
        }
    }

    public function updateCountryCommunity (Request $request) {
        $data = $request->input("data.attributes");
        $communityId = $data['community_id'];
        $country = $data['country_new'];
        $community = Community::whereId($communityId)->first();  //Buscamos la comunidad
        Auth::shouldUse('sanctum'); // Indicar a Laravel que utilice el guard 'sanctum'
        $user = Auth::user(); // Usuario del tokens
        if ($user->id !== $community->user_id) {
            $community->country = $country;
            $community->save();
            return $community;
        } else if ($user->role === 'admin') {
            $community->country = $country;
            $community->save();
            return $community;
        }
    }

    public function updateLanguageCommunity (Request $request) {
        $data = $request->input("data.attributes");
        $communityId = $data['community_id'];
        $language = $data['language_new'];
        $community = Community::whereId($communityId)->first();  //Buscamos la comunidad
        Auth::shouldUse('sanctum'); // Indicar a Laravel que utilice el guard 'sanctum'
        $user = Auth::user(); // Usuario del tokens
        if ($user->id !== $community->user_id) {
            $community->language = $language;
            $community->save();
            return $community;
        } else if ($user->role === 'admin') {
            $community->language = $language;
            $community->save();
            return $community;
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
