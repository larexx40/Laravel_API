<?php

namespace App\Http\Controllers;

use App\Config\APIErrorCode;
use App\Config\APIUserResponse;
use App\Http\Controllers\Controller;
use App\Interfaces\ResetPasswordInterface;
use App\Interfaces\UserRepositoryInterface;
use App\Mail\SendCodeResetPassword;
use App\Mail\UserEmails;
use App\Repositories\UserRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class PasswordResetTokenController extends BaseController
{
    //
    private ResetPasswordInterface $resetPasswordRepository;
    private UserRepositoryInterface $userRepository;
    public function __construct(ResetPasswordInterface $resetPasswordRepository, UserRepositoryInterface $userRepository)
    {
        $this->resetPasswordRepository = $resetPasswordRepository;
        $this->userRepository = $userRepository;
    }


    public function forgetPassword(Request $request)
    {
        $input = $request->only(
            "email",
        );
        $validator = Validator::make($input, [
                'email' => 'required|string|email|exists:users,email',
                // 'password' => 'required|string|min:6|regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{6,}$/',
            ],
            $messages =[
                'email.exists' => 'Email does not exist.',
                // 'password.regex' => 'Password must contain at least one uppercase letter, one lowercase letter, one digit, and one special character (#?!@$%^&*-).',
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
            
            // $userExist = UserRepository::checkIfUserExist("email", $input['email']);
            if(!$userInfo){
                $text = APIUserResponse::$emailNotExist;
                $mainData= [];
                $hint = ['Pass in mail you used to register',"Ensure to use the method stated in the documentation."];
                $linktosolve = "https://";
                $errorCode = APIErrorCode::$internalUserWarning;
                return $this->respondBadRequest($text, $mainData, $hint, $linktosolve, $errorCode);
            }

            // Delete all old code that user send before.
            $this->resetPasswordRepository->deleteAllUserToken($input['email']);
            //generate 4-digit code & add new code
            $input['token'] = mt_rand(1000, 9999);
            $this->resetPasswordRepository->addResetToken($input);

            //send email
            $user = (object) [
                'name' => $userInfo->fname ?? $userInfo->lname ?? $userInfo->username,
                'email' => $input['email'],
            ];
            
            //send mail ithout queue
            // Mail::to($input['email'])->send(
            //     (new UserEmails($user))->passwordResetEmail($user, $input['token'])
            // );
            Mail::to($input['email'])->queue((new UserEmails($user))->passwordResetEmail($user, $input['token']));

            $mainData = [];
            $text = APIUserResponse::$forgotPasswordOTP;
            return $this->respondOK($mainData, $text);
        } catch(\Exception $e){
            return $this->handleException($e);
        }
    }

    public function verifyToken(Request $request)
    {
        $input = $request->only(
            "token",
        );
        $validator = Validator::make($input, [
                'token' => 'required|integer|between:1000,9999',
            ],
            $messages =[
                'token.between' => 'Invalid token.',
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
            // find the code
            $passwordReset = $this->resetPasswordRepository->getTokenByToken($input['token']);
            //check if code is valid
            if (empty($passwordReset)) {
                $text = APIUserResponse::$invalidOTP;
                $mainData = [];
                $hint = ["The token is not in database."];
                $linktosolve = "https://";
                $errorCode = APIErrorCode::$invalidDataSent;
                return $this->respondBadRequest($mainData, $text, $hint, $linktosolve, $errorCode);
            }

            // check if it does not expired: the time is 5 minutes
            $createdAt = $passwordReset->created_at;

            // Calculate the expiration time by adding 5 minutes to the created_at time.
            $expirationTime = Carbon::parse($createdAt)->addMinute(5);

            // Get the current time using Carbon.
            $currentTime = Carbon::now();

            // Compare the current time with the expiration time.
            if ($currentTime > $expirationTime) {                
                $text = APIUserResponse::$OTPExpire;
                $mainData = [];
                $hint = ["Token expired."];
                $linktosolve = "https://";
                $errorCode = APIErrorCode::$invalidDataSent;
                return $this->respondBadRequest($mainData, $text, $hint, $linktosolve, $errorCode);
            }

            $mainData = [];
            $text = APIUserResponse::$validOTP;
            return $this->respondOK($mainData, $text);
        } catch(\Exception $e){
            return $this->handleException($e);
        }
    }

    public function resetPassword(Request $request)
    {
        $input = $request->only(
            "token",
            "password"
        );
        $validator = Validator::make($input, [
                'token' => 'required|integer|between:1000,9999',
                'password' => 'required|string|min:6|regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{6,}$/',
            ],
            $messages =[
                'token.between' => 'Invalid token.',
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
        
        try {   
            // find the code
            $passwordReset = $this->resetPasswordRepository->getTokenByToken($input['token']);
            //check if code is valid
            if (empty($passwordReset)) {
                $text = APIUserResponse::$invalidOTP;
                $mainData = [];
                $hint = ["The token is not in database."];
                $linktosolve = "https://";
                $errorCode = APIErrorCode::$invalidDataSent;
                return $this->respondBadRequest($mainData, $text, $hint, $linktosolve, $errorCode);
            }

            $createdAt = $passwordReset->created_at;

            // Calculate the expiration time by adding 5 minutes to the created_at time.
            $expirationTime = Carbon::parse($createdAt)->addMinute(5);

            // Get the current time using Carbon.
            $currentTime = Carbon::now();

            // Compare the current time with the expiration time.
            if ($currentTime > $expirationTime) {
                
                $text = APIUserResponse::$OTPExpire;
                $mainData = [];
                $hint = ["Token expired."];
                $linktosolve = "https://";
                $errorCode = APIErrorCode::$invalidDataSent;
                return $this->respondBadRequest($mainData, $text, $hint, $linktosolve, $errorCode);
            }

            // update password with user email
            $user = $this->userRepository->getUserByEmail($passwordReset->email);
            $user->password = bcrypt($input['password']);
            $user->save(); 
            
            // delete token from db
            $passwordReset->delete();

            $mainData = [];
            $text = APIUserResponse::$resetPasswordMessage;
            return $this->respondOK($mainData, $text);
        } catch(\Exception $e){
            return $this->handleException($e);
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
