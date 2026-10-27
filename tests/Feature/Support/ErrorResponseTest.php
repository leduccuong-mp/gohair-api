<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

it('returns a {message} JSON body for unknown api routes (404)', function () {
    $this->getJson('/api/v1/does-not-exist')
        ->assertStatus(404)
        ->assertJsonStructure(['message']);
});

it('returns a {message, errors} JSON body for validation failures (422)', function () {
    Route::post('/api/v1/_test/validate', function (Request $request) {
        $request->validate(['name' => 'required|string']);

        return response()->json(['data' => null]);
    });

    $this->postJson('/api/v1/_test/validate', [])
        ->assertStatus(422)
        ->assertJsonStructure(['message', 'errors' => ['name']]);
});

it('returns a {message} JSON body for unauthenticated requests (401)', function () {
    Route::middleware('auth:sanctum')->get('/api/v1/_test/protected', fn () => response()->json(['data' => null]));

    $this->getJson('/api/v1/_test/protected')
        ->assertStatus(401)
        ->assertJsonStructure(['message']);
});

it('returns a {message} JSON body for forbidden requests (403)', function () {
    Route::get('/api/v1/_test/forbidden', fn () => abort(403, 'Forbidden.'));

    $this->getJson('/api/v1/_test/forbidden')
        ->assertStatus(403)
        ->assertJsonStructure(['message']);
});

it('returns a generic {message} JSON body for unexpected errors (500) without leaking debug info', function () {
    config(['app.debug' => false]);

    Route::get('/api/v1/_test/boom', function () {
        throw new RuntimeException('something only developers should see');
    });

    $this->getJson('/api/v1/_test/boom')
        ->assertStatus(500)
        ->assertExactJson(['message' => 'Server Error']);
});
