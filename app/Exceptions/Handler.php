<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $e
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function render($request, Throwable $e): Response
    {
        if ($e instanceof TokenMismatchException) {
            return $this->handleCsrfException($request);
        }

        return parent::render($request, $e);
    }

    /**
     * Handle CSRF token mismatch exceptions.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function handleCsrfException(Request $request): Response
    {
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
