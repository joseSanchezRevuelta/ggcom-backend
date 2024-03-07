<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comment;
use App\Models\Community;
use App\Http\Requests\CreateCommentRequest;
use App\Http\Requests\DeleteCommentRequest;

class CommentController extends Controller
{
    public function getComments (Request $request) {
        // Auth::shouldUse('sanctum');
        // $user = Auth::user();
        $communityId = $request->query('community_id');
        $perPage = $request->query('limit', 24);
        $page = $request->query('page');
        // if ($user->id !== $userid) {
        //     if ($user->role === 'admin') {
        //         $comments = Comment::where('community_id', $communityId)
        //         ->orderBy('created_at', 'desc') // Ordena los comentarios por fecha de creación de forma descendente
        //         ->paginate($perPage, ['*'], 'page', $page);
        //         return $comments;
        //     }
        // } else if ($user->id === $userid) {
        //     $comments = Comment::where('community_id', $communityId)
        //     ->orderBy('created_at', 'desc') // Ordena los comentarios por fecha de creación de forma descendente
        //     ->paginate($perPage, ['*'], 'page', $page);
        //     return $comments;
        // }
        $comments = Comment::where('community_id', $communityId)
                    ->orderBy('created_at', 'desc') // Ordena los comentarios por fecha de creación de forma descendente
                    ->paginate($perPage, ['*'], 'page', $page);
        return $comments;
    }

    public function createComment (CreateCommentRequest $request) {
        $data = $request->input("data.attributes");
        $comment = new Comment($data);
        $comment->save();
        // Incrementar num_comments de la tabla communities
        $communityId = $data['community_id']; // Obtener el ID de la comunidad
        $community = Community::whereId($communityId)->first();  //Buscamos la comunidad
        $community->increment('num_comments'); // Incrementar el campo 'num_members' en 1
        return $data;
    }

    public function deleteComment (DeleteCommentRequest $request) {
        $data = $request->input("data.attributes");
        $id = $data['id'];
        $userId = $data['user_id'];
        $communityId = $data['community_id'];
        // Auth::shouldUse('sanctum');
        // $user = Auth::user();
        // if ($user->id !== $userid) {
        //     if ($user->role === 'admin') {
        //         $comment = Comment::whereId($id)->first();  //Buscamos el comentario
        //         if ($comment) {
        //             $comment->delete();
        //             // Decrementar num_comments de la tabla communities
        //             $communityId = $comment['community_id']; // Obtener el ID de la comunidad
        //             $community = Community::whereId($communityId)->first();  //Buscamos la comunidad
        //             $community->decrement('num_comments'); // Decrementar el campo 'num_members' en 1
        //             return response()->json(['message' => 'Comentario borrado correctamente','comentario' => $comment], 200);
        //         } else {
        //             return response()->json(['error' => 'No se encontró ningun comentario para borrar'], 404);
        //         }
        //     }
        // } else if ($user->id === $userid) {
        //     $comment = Comment::whereId($id)->first();  //Buscamos el comentario
        //     if ($comment) {
        //         $comment->delete();
        //         // Decrementar num_comments de la tabla communities
        //         $communityId = $comment['community_id']; // Obtener el ID de la comunidad
        //         $community = Community::whereId($communityId)->first();  //Buscamos la comunidad
        //         $community->decrement('num_comments'); // Decrementar el campo 'num_members' en 1
        //         return response()->json(['message' => 'Comentario borrado correctamente','comentario' => $comment], 200);
        //     } else {
        //         return response()->json(['error' => 'No se encontró ningun comentario para borrar'], 404);
        //     }
        // } else {
        //     return response()->json(['error' => 'Error, no se borró el comentario'], 404);
        // }

        $comment = Comment::whereId($id)->first();  //Buscamos el comentario
        $comment->delete();
        // Decrementar num_comments de la tabla communities
        $communityId = $comment['community_id']; // Obtener el ID de la comunidad
        $community = Community::whereId($communityId)->first();  //Buscamos la comunidad
        $community->decrement('num_comments'); // Decrementar el campo 'num_members' en 1
        return response()->json(['message' => 'Comentario borrado correctamente','comentario' => $comment], 200);
    }

}
