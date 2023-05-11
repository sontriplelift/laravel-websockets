<?php

namespace App\Http\Controllers;

use App\Http\Resources\CommentResource;
use App\Models\Comment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return ResourceCollection
     */
    public function index(Request $request)
    {
        $pageSize = $request->page_size ?? 20;
        $comments = Comment::query()->paginate($pageSize);

        return CommentResource::collection($comments);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return CommentResource
     */
    public function store(Request $request)
    {
        $created = Comment::query()->create([
            'body' => $request->body,
            'user_id' => $request->user_id,
            'post_id' => $request->post_id,
        ]);
        return new CommentResource($created);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Comment  $comment
     * @return CommentResource
     */
    public function show(Comment $comment)
    {
        return new CommentResource($comment);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Comment  $comment
     * @return CommentResource | JsonResponse
     */
    public function update(Request $request, Comment $comment)
    {
        $updated = $comment->update([
            'body' => $request->body ?? $comment->body,
            'user_id' => $request->user_id ?? $comment->user_id,
            'post_id' => $request->post_id ?? $comment->post_id,
        ]);

        if (!$updated) {
            return new JsonResponse([
                'errors' => [
                    'Update failed.'
                ]
            ]);
        } else {
            return new CommentResource($comment);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Comment  $comment
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Comment $comment)
    {
        $deleted = $comment->forceDelete();

        if (!$deleted) {
            return new JsonResponse([
                'errors' => [
                    'Delete failed.'
                ]
            ]);
        } else {
            return new JsonResponse([
                'data' => 'Delete successfully.'
            ]);
        }
    }
}
