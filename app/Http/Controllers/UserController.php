<?php

namespace App\Http\Controllers;

use App\Config\APIErrorCode;
use App\Config\APIUserResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Utilities\UtilityFunctions;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class UserController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $users = User::orderBy('id', 'desc')->get();
        $text = "Data fetched successfully";
        if(count($users) == 0){
            $text = "No user found";
        }
        $users = UserResource::collection($users);
        return $this->respondOK($users, $text);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserRequest $request)
    {
        //
        $input = $request->only(
            "fname",
            "lname",
            "username",
            "email",
            "password",
            "phoneno",
            'dob',
            'sex',
            'refby',           
        );

        // Validate the request data using the rules specified in UserRequest
        $validator = Validator::make($input, [
            "fname" => "required",
            "lname" => "required",
            "username" => "required",
            "email" => "required",
            "password" => "required",
            "phoneno" => "required",
            'dob' => 'required',
            'sex' => 'required',
        ]);

        //bycrypt the password
        $input['password'] = bcrypt($input['password']);   
        //generate unique userid for user
        $input['userid'] = UtilityFunctions::generateUniqueShortKey("users", "userid");
        $input['userpubkey'] = UtilityFunctions::generateUniquePubKey("users", "userpubkey");

        try{
            //print input
            User::create($input);
            $text = APIUserResponse::$registerSuccess;
            $mainData = [];
            return $this->respondOK($mainData, $text);

        }catch(QueryException $e){
            $errorInfo = $e->errorInfo;
            $text = APIUserResponse::$dbInsertError;
            $mainData= [];
            $hint = ["Ensure to use the method stated in the documentation."];
            $linktosolve = "https://";
            $errorCode = APIErrorCode::$internalInsertDBFatal;
            return $this->respondInternalError($text, $mainData, $errorInfo, $linktosolve, $errorCode);
        }catch(Exception $e){
            return response()->json(['error' => 'An error occurred.'], 500);
        }
        
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
