<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
// use Illuminate\Http\Request;

use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Request;


class BaseController extends Controller
{
    //for success and error response

    public function respondMethodNotAllowed($maindata, $text, $hint, $linktosolve, $errorcode)
    {
        $method = Request::method();
        $endpoint = Request::fullUrl();
        $errordata = ["code" => $errorcode, "text" => "Method used is not valid", "link" => $linktosolve, "hint" => $hint];
        $data = [
            "status" => false,
            "text" => $text,
            "data" => $maindata,
            "time" => now()->format('d-m-y H:i:sA'),
            "method" => $method,
            "endpoint" => $endpoint,
            "error" => $errordata
        ];

        return Response::json($data, 405)->header('Content-Type', 'application/json');
    }

    public function respondBadRequest($maindata, $text, $hint, $linktosolve, $errorcode)
    {
        $method = request()->method();
        $endpoint = request()->fullUrl();

        $errordata = [
            "code" => $errorcode,
            "text" => "Data sent by the user is not valid",
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

        return response()->json($data, 400, [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    public function respondUnauthorized($maindata, $text, $hint, $linktosolve, $errorcode)
    {
        $method = request()->method();
        $endpoint = url()->current();

        $errordata = [
            "code" => $errorcode,
            "text" => "User not authorised",
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

    public function respondOK($maindata, $text)
    {
        $method = request()->method();
        $endpoint = url()->current();

        $errordata = [];
        $data = [
            "status" => true,
            "text" => $text,
            "data" => $maindata,
            "time" => now()->format('d-m-y H:i:sA'),
            "method" => $method,
            "endpoint" => $endpoint,
            "error" => $errordata,
        ];

        return response()->json($data, 200)->header('Content-Type', 'application/json');
    }

    public function respondInternalError($maindata, $text, $hint, $linktosolve, $errorcode)
    {
        $method = request()->method();
        $endpoint = url()->current();

        $errordata = [
            "code" => $errorcode,
            "text" => "Error with DB server",
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

        return response()->json($data, 500)->header('Content-Type', 'application/json');
    }

    public function respondExternalError($maindata, $text, $hint, $linktosolve, $errorcode)
    {
        $method = request()->method();
        $endpoint = url()->current();

        $errordata = [
            "code" => $errorcode,
            "text" => "External service unavailable",
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

        return response()->json($data, 503)->header('Content-Type', 'application/json');
    }

    public function respondValidationError($maindata, $text, $hint, $linktosolve, $errorcode)
    {
        $method = request()->method();
        $endpoint = url()->current();

        $errordata = [
            "code" => $errorcode,
            "text" => "Validation error(s)",
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

        return response()->json($data, 422)->header('Content-Type', 'application/json');
    }
}
