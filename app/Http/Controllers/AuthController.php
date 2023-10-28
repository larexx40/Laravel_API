<?php

namespace App\Http\Controllers;

use App\Config\APIErrorCode;
use App\Config\APIUserResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Models\User;
use App\Utilities\UtilityFunctions;
use Illuminate\Database\QueryException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class AuthController extends BaseController{


    public function login(Request $request)
    {
        $input = $request->only(
            "email",
            "password",
        );
        $validator = Validator::make($input, [
                'email' => 'required|string|email|exists:users,email',
                'password' => 'required|string|min:6|regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{6,}$/',
            ],
            $messages =[
                'email.exists' => 'Email does not exist.',
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

        $token = Auth::attempt($input);
        if (!$token) {       
            $text = APIUserResponse::$invalidLoginError;
            $mainData= [];
            $hint = ["Ensure to use the method stated in the documentation."];
            $linktosolve = "https://";
            $errorCode = APIErrorCode::$internalUserWarning;
            return $this->respondBadRequest($mainData, $text, $hint, $linktosolve, $errorCode);
        }

        // $user = Auth::user();
        $text = APIUserResponse::$loginSuccessful;
        $mainData = [
            'token' => $token,
            'type' => 'Bearer',
        ];
        return $this->respondOK($mainData, $text);
    }

    public function register(Request $request){
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
                'email' => 'required|string|email|unique:users,email',
                'password' => 'required|string|min:6|regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{6,}$/',
                'phoneno' => [ 'string', 'regex:/^[0-9]{11}$/'],
                'dob' => ['date', 'before:today'],
                'sex' => 'required|in:male,female',

                "lname" => "required",
                "username" => "required",
                "phoneno" => "required",
                'dob' => 'required',
                'sex' => 'required',
            ],
            $messages =[
                'email.unique' => 'Email already exists.',
                'password.regex' => 'Password must contain at least one uppercase letter, one lowercase letter, one digit, and one special character (#?!@$%^&*-).',
                'phoneno.regex' => 'The phone number must be 11 digits in length and contain only numbers.',
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
        //generate unique userid for user
        $input['userid'] = UtilityFunctions::generateUniqueShortKey("users", "userid");
        $input['userpubkey'] = UtilityFunctions::generateUniquePubKey("users", "userpubkey");

        try {
            $user = User::create($input);           
            $token = Auth::login($user);
            $text = APIUserResponse::$registerSuccess;
            $mainData = [
                'token' => $token,
                'type' => 'bearer',
            ];
            return $this->respondOK($mainData, $text);
            
        }catch(QueryException $e){
            $errorInfo = $e->errorInfo;
            $text = APIUserResponse::$dbInsertError;
            $mainData= [];
            $hint = ["Ensure to use the method stated in the documentation."];
            $linktosolve = "https://";
            $errorCode = APIErrorCode::$internalInsertDBFatal;
            return $this->respondInternalError($text, $mainData, $errorInfo, $linktosolve, $errorCode);
        
        } catch (\Exception $e) {
            $errorInfo = $e->getMessage();
            $text = APIUserResponse::$dbInsertError;
            $mainData= [];
            $hint = ["Ensure to use the method stated in the documentation."];
            $linktosolve = "https://";
            $errorCode = APIErrorCode::$internalInsertDBFatal;
            return $this->respondInternalError($text, $mainData, $errorInfo, $linktosolve, $errorCode);
        }
        
    }

    public function logout()
    {
        Auth::logout();
        return response()->json([
            'status' => 'success',
            'message' => 'Successfully logged out',
        ]);
    }

    public function me()
    {
        $token = request()->header('Authorization');
        // $userDetails =  JWTAuth::toUser($token);
        $userDetails= Auth::user();
        unset($userDetails->password);
        unset($userDetails->userpubkey);
        
        return response()->json([
            'status' => 'success',
            'user' => $userDetails,
        ]);
    }

    public function refresh()
    {
        return response()->json([
            'status' => 'success',
            'user' => Auth::user(),
            'authorisation' => [
                'token' => Auth::refresh(),
                'type' => 'bearer',
            ]
        ]);
    }


    public function adminLogin(Request $request)
    {
        $input = $request->only(
            "email",
            "password",
        );
        $validator = Validator::make($input, [
                'email' => 'required|string|email|exists:admins,email',
                'password' => 'required|string|min:6|regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{6,}$/',
            ],
            $messages =[
                'email.exists' => 'Email does not exist.',
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

        //admin login 
        $token = Auth::guard('admin')->attempt($input);
        if (!$token) {            
            $errorInfo ='';
            $text = APIUserResponse::$invalidLoginError;
            $mainData= [];
            $hint = ["Ensure to use the method stated in the documentation."];
            $linktosolve = "https://";
            $errorCode = APIErrorCode::$internalUserWarning;
            return $this->respondBadRequest($text, $mainData, $errorInfo, $linktosolve, $errorCode);
        }

        // $admin = Auth::guard('admins')->user();
        $text = APIUserResponse::$loginSuccessful;
        $mainData = [
            'token' => $token,
            'type' => 'Bearer',
        ];
        return $this->respondOK($mainData, $text);
    }

    public function adminDetails()
    {
        $adminDetails= Auth::guard('admin')->user();
        $adminDetails['status_value'] = $adminDetails->status = 1 ? "Active" : "Banned";
        $adminDetails['created_at'] = $adminDetails->created_at->format('d-m-Y');
        unset($adminDetails->password);
        unset($adminDetails->adminpubkey);
        // unset($adminDetails->updated_at);
        
        return response()->json([
            'status' => 'success',
            'user' => $adminDetails,
        ]);
    }

    
    
}
