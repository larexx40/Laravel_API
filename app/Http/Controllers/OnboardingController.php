<?php

namespace App\Http\Controllers;

use App\Config\APIErrorCode;
use App\Config\APIUserResponse;
use App\Http\Controllers\Controller;
use App\Interfaces\UserRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OnboardingController extends BaseController
{
    //
    private UserRepositoryInterface $userRepository;
    public function __construct(UserRepositoryInterface $userRepository){
        $this->userRepository = $userRepository;
    }

    public function sendVerifyMailOTP(Request $request){
        $input = $request->only(
            "email",
        );
        $validator = Validator::make($input, [
                'email' => 'required|string|email|exists:users,email',
            ],
            $messages =[
                'email.exists' => 'Email does not exist.',
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

        try {
            //check if email exist
            $userInfo = $this->userRepository->getUserData("email", $input['email'], ['fname', 'lname', 'username']);
            if(!$userInfo){
                $text = APIUserResponse::$emailNotExist;
                $mainData= [];
                $hint = ['Pass in mail you used to register',"Ensure to use the method stated in the documentation."];
                $linktosolve = "https://";
                $errorCode = APIErrorCode::$internalUserWarning;
                return $this->respondBadRequest($text, $mainData, $hint, $linktosolve, $errorCode);
            }

            //delete previous token
            //add new 4 digit otp
            

        } catch (\Throwable $th) {
            //throw $th;
        }
    }

    protected function handleException(\Exception $e)
    {
        $errorInfo = $e->getMessage();
        $text = APIUserResponse::$unExpectedError;
        $mainData= [];
        $hint = ["Ensure to use the method stated in the documentation."];
        $linktosolve = "https://";
        $errorCode = APIErrorCode::$internalInsertDBFatal;
        return $this->respondInternalError($mainData, $text, $errorInfo, $linktosolve, $errorCode);
    }

}
