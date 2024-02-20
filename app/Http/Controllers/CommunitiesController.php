<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Community;
use App\Models\Comment;
use App\Models\JoinCommunity;
use App\Http\Requests\CreateCommunityRequest;
use App\Http\Requests\JoinCommunityRequest;
use App\Http\Requests\LeaveCommunityRequest;
use App\Http\Requests\DeleteCommunityRequest;

class CommunitiesController extends Controller
{
    public function getCommunities () {

        return "<h1>AQUI ESTAMOS EN EL CONTROLLER</h1>";
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
        // Incrementar numPersons de la tabla communities
        $communityId = $data['community_id']; // Obtener el ID de la comunidad
        $community = Community::whereId($communityId)->first();  //Buscamos la comunidad
        $community->increment('num_persons'); // Incrementar el campo 'num_members' en 1
        return $joinCommunity;
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
            // Decrementar numPersons de la tabla communities
            $community = Community::whereId($community_id)->first();  //Buscamos la comunidad
            $community->decrement('numPersons');
            return response()->json(['message' => 'Comunidad dejada correctamente','joinComunity' => $joinCommunity], 200);
        } else {
            return response()->json(['error' => 'No se encontró ningun user-comunidad para dejar'], 404);
        }
    }

    public function deleteCommunity (DeleteCommunityRequest $request) {
        $data = $request->input("data.attributes");
        $id = $data['id'];
        $community = Community::whereId($id)->first();  //Buscamos la comunidad
        if ($community) {
            Comment::where('community_id', $id)->delete();  //Primero borramos todos los comentarios
            $community->delete();   //Despues borramos la comunidad
            return response()->json(['message' => 'Comunidad borrada correctamente','communidad' => $community], 200);
        } else {
            return response()->json(['error' => 'No se encontró ninguna comunidad para borrar'], 404);
        }
        return $community;
    }

    public function updateCommunity (LeaveCommunityRequest $request) {

    }
}
