<?php

namespace Tests\Feature\Api\V1\Post;

use App\Events\Models\Post\PostCreated;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class PostApiTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $user = User::factory(1)->create()->first();
        $this->actingAs($user);
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }

    public function test_index()
    {
        // load data in db
        $posts = Post::factory(10)->create();
        $postIds = $posts->map(fn ($post) => $post->id);

        // call index endpoint
        $response = $this->json('get', '/api/v1/posts');

        // assert status
        $response->assertStatus(200);

        // verify records
        $data = $response->json('data');
        collect($data)->each(fn ($post) => $this->assertTrue(in_array($post['id'], $postIds->toArray())));
    }

    public function test_show()
    {
        $dummy = Post::factory()->create();
        $response = $this->json('get', '/api/v1/posts/' . $dummy->id);

        $result = $response->assertStatus(200)->json('data');

        $this->assertEquals(data_get($result, 'id'), $dummy->id, 'Response ID is not the same as model id.');
    }

    public function test_create()
    {
        Event::fake();
        $dummy = Post::factory()->make();
        $response = $this->json('post', '/api/v1/posts', $dummy->toArray());
        Event::assertDispatched(PostCreated::class);
        $result = $response->assertStatus(201)->json('data');
        $result = collect($result)->only(array_keys($dummy->getAttributes()));
        $result->each(function ($value, $field) use ($dummy) {
            $this->assertSame(data_get($dummy, $field), $value, 'Fillable is not the same.');
        });
    }

    public function test_update()
    {
        $dummy = Post::factory()->create();
        $dummy2 = Post::factory()->make();

        $fillables = collect((new Post())->getFillable());

        $fillables->each(function ($fillable) use ($dummy, $dummy2) {
            $response = $this->json('patch', '/api/v1/posts/' . $dummy->id, [
                $fillable => data_get($dummy2, $fillable)
            ]);

            $result = $response->assertStatus(200)->json('data');
            $this->assertSame(data_get($dummy2, $fillable), data_get($dummy->refresh(), $fillable), 'Failed to update model.');
        });
    }

    public function test_delete()
    {
        $dummy = Post::factory()->create();

        $response = $this->json('delete', '/api/v1/posts/' . $dummy->id);

        $response->assertStatus(200);
        $this->expectException(ModelNotFoundException::class);
        Post::query()->findOrFail($dummy->id);
    }
}
