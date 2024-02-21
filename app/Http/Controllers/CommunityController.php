<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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
    public function getCommunities (Request $request) {
        if ($request->input('language')) {  //Language
            $language = $request->input('language');
            $communities = Community::where('language', $language)->get();
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
        } else {
            //Popular
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
    }

    public function getCommunitiesLanguage(Request $request) {
        $language = $request->input('language');
        $communities = Community::where('language', $language)->get();
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
