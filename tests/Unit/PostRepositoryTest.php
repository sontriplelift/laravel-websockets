<?php

namespace Tests\Unit;

use App\Exceptions\GeneralJsonException;
use App\Models\Post;
use App\Repositories\PostRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostRepositoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_create()
    {
        // 1. Define the goal:
        //    Test if create() will actually create a record in the DB

        // 2. Replicate the env / restriction
        $repository = $this->app->make(PostRepository::class);

        // 3. Define the source of truth
        $payload = [
            'title' => 'Lococddsi',
            'body' => []
        ];

        // 4. Compare the result
        $result = $repository->create($payload);

        $this->assertSame(strtolower($payload['title']), $result->title, 'Post cretaed does not have the same title.');
    }

    public function test_update()
    {
        $repository = $this->app->make(PostRepository::class);
        $dummy = Post::factory(1)->create()[0];
        $payload = [
            'title' => 'ABCD'
        ];
        $repository->update($dummy, $payload);
        $this->assertSame(strtolower($payload['title']), $dummy->title, 'Post updated does not have the same title.');
    }

    public function test_delete()
    {
        $repository = $this->app->make(PostRepository::class);
        $dummy = Post::factory(1)->create()->first();
        $repository->forceDelete($dummy);
        $found = Post::query()->find($dummy->id);
        $this->assertSame(null, $found, 'Post is not deleted.');
    }

    public function test_delete_will_throw_exception_when_delete_post_that_doesnt_exist()
    {
        $repository = $this->app->make(PostRepository::class);
        $dummy = Post::factory(1)->make()->first();
        $this->expectException(GeneralJsonException::class);
        $repository->forceDelete($dummy);
    }
}
