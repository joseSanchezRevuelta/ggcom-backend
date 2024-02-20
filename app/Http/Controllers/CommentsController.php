<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comment;
use App\Http\Requests\CreateCommentRequest;
use App\Http\Requests\DeleteCommentRequest;

class CommentsController extends Controller
{
    public function createComment (CreateCommentRequest $request) {
        $data = $request->input("data.attributes");
        $comment = new Comment($data);
        $comment->save();
        // Incrementar numComments de la tabla communities
        $communityId = $data['community_id']; // Obtener el ID de la comunidad
        $community = Community::whereId($communityId)->first();  //Buscamos la comunidad
        $community->increment('num_coments'); // Incrementar el campo 'num_members' en 1
        return $comment;
    }

    public function deleteComment (DeleteCommentRequest $request) {
        $data = $request->input("data.attributes");
        $id = $data['id'];
        $comment = Comment::whereId($id)->first();  //Buscamos el user
        if ($comment) {
            $comment->delete();
            // Decrementar numComments de la tabla communities
            $communityId = $data['community_id']; // Obtener el ID de la comunidad
            $community = Community::whereId($communityId)->first();  //Buscamos la comunidad
            $community->decrement('num_coments'); // Incrementar el campo 'num_members' en 1
            return response()->json(['message' => 'Comentario borrado correctamente','comentario' => $comment], 200);
        } else {
            return response()->json(['error' => 'No se encontró ningun comentario para borrar'], 404);
        }
    }
}
