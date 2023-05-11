<?php

namespace App\Repositories;

use App\Events\Models\User\UserCreated;
use App\Events\Models\User\UserDeleted;
use App\Events\Models\User\UserUpdated;
use App\Exceptions\GeneralJsonException;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserRepository extends BaseRepository
{
    public function create(array $attributes)
    {
        return DB::transaction(function () use ($attributes) {
            $created = User::query()->create([
                'name' => data_get($attributes, 'name'),
                'email' => data_get($attributes, 'email'),
                'password' => Hash::make(data_get($attributes, 'password')),
            ]);

            throw_if(!$created, GeneralJsonException::class, 'Failed to create user');

            event(new UserCreated($created));

            return $created;
        });
    }

    public function update($user, array $attributes)
    {
        return DB::transaction(function () use ($user, $attributes) {
            $updated = $user->update([
                'name' => data_get($attributes, 'name', $user->name),
                'email' => data_get($attributes, 'email', $user->email),
            ]);

            throw_if(!$updated, GeneralJsonException::class, 'Failed to update user');

            event(new UserUpdated($user));

            return $user;
        });
    }

    public function forceDelete($user)
    {
        return DB::transaction(function () use ($user) {
            $deleted = $user->forceDelete();

            throw_if(!$deleted, GeneralJsonException::class, 'Cannot delete user');

            event(new UserDeleted($deleted));

            return $deleted;
        });
    }
}
