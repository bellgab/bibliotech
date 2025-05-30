<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

// Debug route to test CSRF token generation and session
Route::get('/debug/csrf', function (Request $request) {
    return response()->json([
        'csrf_token' => csrf_token(),
        'session_id' => session()->getId(),
        'session_driver' => config('session.driver'),
        'session_cookie' => config('session.cookie'),
        'app_key_exists' => !empty(config('app.key')),
        'session_lifetime' => config('session.lifetime'),
        'secure_cookie' => config('session.secure'),
        'headers' => $request->headers->all(),
    ]);
})->middleware('web');

Route::post('/debug/csrf-test', function (Request $request) {
    return response()->json([
        'message' => 'CSRF verification passed!',
        'token_from_request' => $request->input('_token'),
        'token_from_session' => session()->token(),
        'session_id' => session()->getId(),
    ]);
})->middleware('web');
