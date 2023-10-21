<?php
// app/Exceptions/JWTExceptionHandler.php

use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException as ExceptionsJWTException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenExpiredException as ExceptionsTokenExpiredException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenInvalidException;

class JWTExceptionHandler extends ExceptionHandler
{
    protected $dontReport = [
        TokenExpiredException::class,
        TokenInvalidException::class,
        JWTException::class,
    ];

    public function render($request, Exception $exception)
    {
        if ($exception instanceof ExceptionsTokenExpiredException) {
            return response()->json(['error' => 'Token is expired'], 401);
        } elseif ($exception instanceof TokenInvalidException) {
            return response()->json(['error' => 'Token is invalid'], 401);
        } elseif ($exception instanceof ExceptionsJWTException) {
            return response()->json(['error' => 'JWT error'], 401);
        }

        return parent::render($request, $exception);
    }
}