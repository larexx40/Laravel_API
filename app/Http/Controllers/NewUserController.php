<?php

namespace App\Http\Controllers;
use App\Config\APIErrorCode;
use App\Config\APIUserResponse;
use App\Interfaces\UserRepositoryInterface;
use App\Http\Controllers\Controller;
use App\Http\Controllers\BaseController;
use App\Http\Resources\UserResource;
use App\Utilities\UtilityFunctions;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NewUserController extends BaseController
{
    //
    private UserRepositoryInterface $userRepository ;
    public function __construct(UserRepositoryInterface $userRepository){
        $this->userRepository = $userRepository;
    }

    public function getAllUsers()
    {
        $users =  $this->userRepository->getAllUsers();
        $text = "Data fetched successfully";
        if(count($users) == 0){
            $text = "No user found";
        }
        $users = UserResource::collection($users);
        return $this->respondOK($users, $text);
    }

    public function getUserById(String $userid){
        // $userid = $request->userid;
        return $this->userRepository->getUserById($userid);
    }

    public function addNewUser(Request $request){
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
                "fname" => "string|min:3|max:50",
                "lname" => "string|min:3|max:50",
                "username" => "string|min:3|max:50|unique:users,username",
                'email' => 'required|string|email|unique:users,email',
                'password' => 'required|string|min:6|regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{6,}$/',
                'phoneno' => [ 'string', 'regex:/^[0-9]{11}$/'],
                'dob' => "date|before:today",
                'sex' => 'in:male,female',
            ],
            $messages =[
                'username.unique' => 'Username already exists.',
                'email.unique' => 'Email already exists.',
                'password.regex' => 'Password must contain at least one uppercase letter, one lowercase letter, one digit, and one special character (#?!@$%^&*-).',
            ]
        );

        if ($validator->fails()) {
            $text = APIUserResponse::$respondValidationError;
            $mainData= [];
            $hint = $validator->errors()->all();
            $linktosolve = "https://";
            $errorCode = APIErrorCode::$internalUserWarning;
            return $this->respondValidationError($mainData, $text, $hint, $linktosolve, $errorCode);
        }

        //bycrypt the password
        $input['password'] = bcrypt($input['password']);   
        $input['userid'] = UtilityFunctions::generateUniqueShortKey("users", "userid");
        $input['userpubkey'] = UtilityFunctions::generateUniquePubKey("users", "userpubkey");

        try{
            //print input
            $newUser =  $this->userRepository->createUser($input);
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
        }catch(\Exception $e){
            $errorInfo = $e->getMessage();
            $text = APIUserResponse::$unExpectedError;
            $mainData= [];
            $hint = ["Ensure to use the method stated in the documentation."];
            $linktosolve = "https://";
            $errorCode = APIErrorCode::$internalInsertDBFatal;
            return $this->respondInternalError($mainData, $text, $errorInfo, $linktosolve, $errorCode);
        }

    }
}