<?php

use App\Events\ChatMessageEvent;
use App\Events\PlaygroundEvent;
use App\Mail\WelcomeMail;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/app', function () {
    return view('app');
});

Route::get('/reset-password/{token}', function ($token) {
    return view('auth.password-reset', [
        'token' => $token
    ]);
})->middleware(['guest:'.config('fortify.guard')])
  ->name('password.reset');

Route::get('/shared/posts/{post}', function (Request $request, Post $post) {
    return "Hehehe Post id: {$post->id}";
})->name('shared.post')->middleware('signed');

if (App::environment('local')) {
    Route::get('/playground', function () {
        // $user = User::factory()->make();
        // Mail::to($user)->send(new WelcomeMail($user));
        // return null;

        // $url = URL::temporarySignedRoute('share-video', now()->addSeconds(30), [
        //     'video' => 123
        // ]);
        // return $url;
        event(new PlaygroundEvent());
        return null;
    });

    Route::get('/shared/videos/{video}', function (Request $request, $video) {
        return 'git gud';
    })->name('share-video')->middleware('signed');

    Route::get('/ws', function() {
        return view('websocket');
    });

    Route::post('chat-message', function(Request $request) {
        event(new ChatMessageEvent($request->message));
        return null;
    });
}
