<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;
use Symfony\Component\HttpFoundation\Response;

class HandleCsrfErrors
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            return $next($request);
        } catch (TokenMismatchException $e) {
            // Log the CSRF error for debugging
            \Log::warning('CSRF Token Mismatch', [
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'session_id' => $request->session()->getId(),
                'has_session' => $request->hasSession(),
                'token_provided' => $request->input('_token') ? 'yes' : 'no',
                'user_id' => auth()->id(),
                'timestamp' => now()->toISOString()
            ]);

            // If it's an AJAX request, return JSON response
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Page expired. Please refresh and try again.',
                    'code' => 419,
                    'csrf_token' => csrf_token()
                ], 419);
            }

            // For regular requests, redirect back with error and new token
            return redirect()->back()
                ->withInput($request->except(['_token', 'password', 'password_confirmation']))
                ->withErrors([
                    'csrf' => 'Your session has expired. Please try again.'
                ])
                ->with('csrf_token', csrf_token());
        }
    }
}
