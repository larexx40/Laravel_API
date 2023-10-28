<?php

namespace App\Http\Middleware;

use App\Config\APIErrorCode;
use App\Config\APIUserResponse;
use Closure;
use Illuminate\Http\Request;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException;
use Symfony\Component\HttpFoundation\Response;

class AdminJWTMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        try {
            // $admin = JWTAuth::guard('admin')->parseToken()->authenticate();
            $admin = auth()->guard('admin')->user(); // Authenticate admin user
            if (!$admin) {
                $maindata = [];
                $text = APIUserResponse::$unauthorizedToken;
                $errorcode = APIErrorCode::$internalUserWarning;
                $linktosolve = "https://";
                $hint = ["Authorization header must be sent with the correct format","Check if all header values are sent correctly.", "Login to generate anotherauthorization header"];

                return $this->respondUnauthorized($maindata, $text, $hint, $linktosolve, $errorcode);
            }
        } catch (JWTException $e) {
            $hintMessage = $e->getMessage();
            $maindata = [];
            $text = APIUserResponse::$unauthorizedToken;
            $errorcode = APIErrorCode::$internalUserWarning;
            $linktosolve = "https://";
            $hint = ["Authorization header must be sent with the correct format","Check if all header values are sent correctly.", "Login to generate anotherauthorization header", $hintMessage];
            return $this->respondUnauthorized($maindata, $text, $hint, $linktosolve, $errorcode);
        }

        return $next($request);
    }

    public function respondUnauthorized($maindata, $text, $hint, $linktosolve, $errorcode)
    {
        $method = request()->method();
        $endpoint = url()->current();

        $errordata = [
            "code" => $errorcode,
            "text" => "User data is wrong",
            "link" => $linktosolve,
            "hint" => $hint,
        ];

        $data = [
            "status" => false,
            "text" => $text,
            "data" => $maindata,
            "time" => now()->format('d-m-y H:i:sA'),
            "method" => $method,
            "endpoint" => $endpoint,
            "error" => $errordata,
        ];

        return response()->json($data, 401)->header('Content-Type', 'application/json');
    }
}