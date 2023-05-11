<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return ResourceCollection
     */
    public function index()
    {
        $users = User::query()->get();

        return UserResource::collection($users);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return UserResource
     */
    public function store(Request $request)
    {
        $created = User::query()->create([
            'title' => $request->title,
            'body' => $request->body
        ]);
        return new UserResource($created);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return UserResource
     */
    public function show(User $user)
    {
        return new UserResource($user);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return UserResource | JsonResponse
     */
    public function update(Request $request, User $user)
    {
        $updated = $user->update([
            'title' => $request->title ?? $user->title,
            'body' => $request->body ?? $user->body
        ]);

        if (!$updated) {
            return new JsonResponse([
                'errors' => [
                    'Update failed.'
                ]
            ]);
        } else {
            return new UserResource($user);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(User $user)
    {
        $deleted = $user->forceDelete();

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
