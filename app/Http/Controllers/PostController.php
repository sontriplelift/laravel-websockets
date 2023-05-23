<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostStoreRequest;
use App\Http\Resources\PostResource;
use App\Models\Post;
use App\Models\User;
use App\Notifications\PostSharedNotification;
use App\Repositories\PostRepository;
use App\Rules\IntegerArrayRule;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return ResourceCollection
     */
    public function index(Request $request)
    {
        $pageSize = $request->page_size ?? 20;
        $posts = Post::query()->paginate($pageSize);

        return PostResource::collection($posts);
    }

    // /**
    //  * Store a newly created resource in storage.
    //  *
    //  * @param  PostStoreRequest  $request
    //  * @return PostResource
    //  */
    // public function store(PostStoreRequest $request, PostRepository $repository)
    // {
    //     $created = $repository->create($request->only([
    //         'title',
    //         'body',
    //         'user_ids'
    //     ]));

    //     return new PostResource($created);
    // }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return PostResource
     */
    public function store(Request $request, PostRepository $repository)
    {
        $payload = $request->only([
            'title',
            'body',
            'user_ids'
        ]);
        Validator::validate($payload,
            [
                'title' => ['string', 'required'],
                'body' => ['string', 'required'],
                'user_ids' => ['array',
                    'required',
                    new IntegerArrayRule()
                ]
            ],
            [
                'body.required' => 'Please enter a value for body.',
            ],
            [
                'user_ids' => 'user ids'
            ]
        );

        $created = $repository->create($payload);

        return new PostResource($created);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Post  $post
     * @return PostResource
     */
    public function show(Post $post)
    {
        return new PostResource($post);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Post  $post
     * @return PostResource | JsonResponse
     */
    public function update(Request $request, Post $post, PostRepository $repository)
    {
        $post = $repository->update($post, $request->only([
            'title',
            'body',
            'user_ids'
        ]));
        return new PostResource($post);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Post $post, PostRepository $repository)
    {
        $post = $repository->forceDelete($post);
        return new JsonResponse([
            'data' => 'Delete successfully.'
        ]);
    }

    public function share(Request $request, Post $post) {
        $url = URL::temporarySignedRoute('shared.post', now()->addDays(30), [
            'post' => $post->id,
        ]);

        $users = User::query()->whereIn('id', $request->user_ids)->get();

        Notification::send($users, new PostSharedNotification($post, $url));
        // $user = User::query()->find(1);
        // $user->notify(new PostSharedNotification($post, $url));

        return new JsonResponse([
            'data' => $url,
        ]);
    }
}
