<?php

namespace App\Repositories;

abstract class BaseRepository
{
    abstract public function create(array $attribute);
    abstract public function update($model, array $attribute);
    abstract public function forceDelete($model);
}
