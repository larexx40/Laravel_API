<?php

namespace App\Http\Controllers;

use App\Config\APIErrorCode;
use App\Config\APIUserResponse;
use App\Http\Controllers\Controller;
use App\Interfaces\UserRepositoryInterface;
use App\Interfaces\UserTokenInterface;
use App\Mail\UserEmails;
use App\Utilities\UtilityFunctions;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class OnboardingController extends BaseController
{
    //
    private UserRepositoryInterface $userRepository;
    private UserTokenInterface $userTokenRepository;
    public function __construct(UserRepositoryInterface $userRepository, UserTokenInterface $userTokenRepository){
        $this->userRepository = $userRepository;
        $this->userTokenRepository = $userTokenRepository;
    }

    public function registerEmail(Request $request){
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
            //create new user
            $input['userid'] = UtilityFunctions::generateUniqueShortKey("users", "userid");
            $input['userpubkey'] = UtilityFunctions::generateUniquePubKey("users", "userpubkey");

            $newUser = $this->userRepository->createUser($input);
            //send welcome email
            //create otp
            $otp = UtilityFunctions::generateUniqueNumericKey("user_tokens", "token", 4);

            // create verification token details
            $tokenDetails = [
                'userid' => $input['userid'],
                'user_identity' => $input['email'],
                'identity_type' => 1, //1-Email 2-Phone
                'token' => $otp,
                'token_type' => 1, //1-Verify 2-Reset
                'expire_at' => date('Y-m-d H:i:s', strtotime('+5 minutes'))
            ];
            //add token to db
            $this->userTokenRepository->addUserToken($tokenDetails);
            Mail::to($input['email'])->queue((new UserEmails($input))->emailVerificationEmail($input['token']));

            $mainData = [];
            $text = APIUserResponse::$OTPSentViaMail;
            return $this->respondOK($mainData, $text);


        } catch(QueryException $e){
            return $this->handleQueryException($e);
        }catch(\Exception $e){
            return $this->handleException($e);
        }
    }

    public function verifyMail(Request $request){
        $input = $request->only(
            "email",
            "otp"
        );

        $validator = Validator::make($input, [
                'email' => 'required|string|email|exists:users,email',
                'otp' => 'required|string|max:4',
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
            //verify otp
            $token = $this->userTokenRepository->getTokenByToken($input['token']);
            if(!$token){
               $text = APIUserResponse::$invalidOTP;
               $mainData= [];
               $hint = ["check your email for 4 digit OTP","Ensure to use the method stated in the documentation."];
               $linktosolve = "https://";
               $errorCode = APIErrorCode::$internalUserWarning;
               return $this->respondBadRequest($mainData, $text, $hint, $linktosolve, $errorCode);
            }

            if($token['token_type'] != 1){
                $text = APIUserResponse::$invalidOTP;
                $mainData= [];
                $hint = ["check your email for 4 digit OTP","Ensure to use the method stated in the documentation."];
                $linktosolve = "https://";
                $errorCode = APIErrorCode::$internalUserWarning;
                return $this->respondBadRequest($mainData, $text, $hint, $linktosolve, $errorCode);
            }

            if($token['user_identity'] != $input['email']){
                $text = APIUserResponse::$invalidOTP;
                $mainData= [];
                $hint = ["check your email for 4 digit OTP","Ensure to use the method stated in the documentation."];
                $linktosolve = "https://";
                $errorCode = APIErrorCode::$internalUserWarning;
                return $this->respondBadRequest($mainData, $text, $hint, $linktosolve, $errorCode);
            }

            if($token['expire_at'] < date('Y-m-d H:i:s')){
                $text = APIUserResponse::$tokenExpired;
                $mainData= [];
                $hint = ["Expired, resend another otp","Ensure to use the method stated in the documentation."];
                $linktosolve = "https://";
                $errorCode = APIErrorCode::$internalUserWarning;
                return $this->respondBadRequest($mainData, $text, $hint, $linktosolve, $errorCode);
            }

            //verify user email
            $user = $this->userRepository->verifyUserEmail($token['userid']);
            if(!$user){
                $text = APIUserResponse::$unableToVerifyMail;
                $mainData= [];
                $hint = ["DB server Error","Ensure to use the method stated in the documentation."];
                $linktosolve = "https://";
                $errorCode = APIErrorCode::$internalUserWarning;
                return $this->respondInternalError($mainData, $text, $hint, $linktosolve, $errorCode);
            }

            //authenticate user
            $token = Auth::login($user);
            $text = APIUserResponse::$successEmail;
            $mainData = [
                'token' => $token,
                'type' => 'bearer',
            ];
            return $this->respondOK($mainData, $text);

        }catch(QueryException $e){
            return $this->handleQueryException($e);
        }catch(\Exception $e){
            return $this->handleException($e);
        }
    }

    public function registerPhone(Request $request){
        $input = $request->only(
            "phoneno",
        );
        $validator = Validator::make($input, [
                "phoneno"=> "required|regex:/^\+?[0-9]\d{1,20}$/",
            ],
            $messages =[
                "phoneno.regex"=> "Invalid phone number",
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

            $userid = Auth::user()->userid;
            //update user in db
            $newUserDetails = $this->userRepository->updateUser($userid, $input);
            //send welcome email
            //create otp
            $otp = UtilityFunctions::generateUniqueNumericKey("user_tokens", "token", 4);

            // create verification token details
            $tokenDetails = [
                'userid' => $userid,
                'user_identity' => $input['phoneno'],
                'identity_type' => 2, //1-Email 2-Phone
                'token' => $otp,
                'token_type' => 1, //1-Verify 2-Reset
                'expire_at' => date('Y-m-d H:i:s', strtotime('+5 minutes'))
            ];
            //add token to db
            $this->userTokenRepository->addUserToken($tokenDetails);

            //change to sms
            Mail::to($input['email'])->queue((new UserEmails($input))->emailVerificationEmail($input['token']));

            $mainData = [];
            $text = APIUserResponse::$forgotPasswordOTP;
            return $this->respondOK($mainData, $text);


        } catch(QueryException $e){
            return $this->handleQueryException($e);
        }catch(\Exception $e){
            return $this->handleException($e);
        }
    }

    public function verifyPhone(Request $request){
        $input = $request->only(
            "phoneno",
            "otp"
        );

        $validator = Validator::make($input, [
                "phoneno"=> "required|regex:/^\+?[0-9]\d{1,20}$/",
                'otp' => 'required|string|max:4',
            ],
            $messages =[
                "phoneno.regex"=> "Invalid phone number",
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
            //verify otp
            $token = $this->userTokenRepository->getTokenByToken($input['token']);
            if(!$token){
               $text = APIUserResponse::$invalidOTP;
               $mainData= [];
               $hint = ["check your phone SMS for 4 digit OTP","Ensure to use the method stated in the documentation."];
               $linktosolve = "https://";
               $errorCode = APIErrorCode::$internalUserWarning;
               return $this->respondBadRequest($mainData, $text, $hint, $linktosolve, $errorCode);
            }

            if($token['token_type'] != 2){
                $text = APIUserResponse::$invalidOTP;
                $mainData= [];
                $hint = ["check your phone SMS for 4 digit OTP","Ensure to use the method stated in the documentation."];
                $linktosolve = "https://";
                $errorCode = APIErrorCode::$internalUserWarning;
                return $this->respondBadRequest($mainData, $text, $hint, $linktosolve, $errorCode);
            }

            if($token['user_identity'] != $input['phoneno']){
                $text = APIUserResponse::$invalidOTP;
                $mainData= [];
                $hint = ["check your phone SMS for 4 digit OTP","Ensure to use the method stated in the documentation."];
                $linktosolve = "https://";
                $errorCode = APIErrorCode::$internalUserWarning;
                return $this->respondBadRequest($mainData, $text, $hint, $linktosolve, $errorCode);
            }

            if($token['expire_at'] < date('Y-m-d H:i:s')){
                $text = APIUserResponse::$tokenExpired;
                $mainData= [];
                $hint = ["Expired, resend another otp","Ensure to use the method stated in the documentation."];
                $linktosolve = "https://";
                $errorCode = APIErrorCode::$internalUserWarning;
                return $this->respondBadRequest($mainData, $text, $hint, $linktosolve, $errorCode);
            }

            //verify user email
            $user = $this->userRepository->verifyUserPhone($input['phoneno']);
            if(!$user){
                $text = APIUserResponse::$unableToVerifyPhone;
                $mainData= [];
                $hint = ["DB server Error","Ensure to use the method stated in the documentation."];
                $linktosolve = "https://";
                $errorCode = APIErrorCode::$internalUserWarning;
                return $this->respondInternalError($mainData, $text, $hint, $linktosolve, $errorCode);
            }

            //authenticate user
            // $token = Auth::login($user);
            $text = APIUserResponse::$successPhoneVerify;
            $mainData = [];
            return $this->respondOK($mainData, $text);

        }catch(QueryException $e){
            return $this->handleQueryException($e);
        }catch(\Exception $e){
            return $this->handleException($e);
        }
    }

    public function resendMailOTP(Request $request){
        $input = $request->only(
            'email'
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
            $user = $this->userRepository->getUserByEmail($input['email']);
            if(!$user){
                $text = APIUserResponse::$userEmailNotExist;
                $mainData= [];
                $hint = ["DB server Error","Ensure to use the method stated in the documentation."];
                $linktosolve = "https://";
                $errorCode = APIErrorCode::$internalUserWarning;
                return $this->respondInternalError($mainData, $text, $hint, $linktosolve, $errorCode);
            }

            //detlete user token
            $this->userTokenRepository->deleteAllUserToken($input['email']);

            //create another token
            $otp = UtilityFunctions::generateUniqueNumericKey("user_tokens", "token", 4);

            // create verification token details
            $tokenDetails = [
                'userid' => $user['userid'],
                'user_identity' => $input['email'],
                'identity_type' => 1, //1-Email 2-Phone
                'token' => $otp,
                'token_type' => 1, //1-Verify 2-Reset
                'expire_at' => date('Y-m-d H:i:s', strtotime('+5 minutes'))
            ];

            //add token to db
            $this->userTokenRepository->addUserToken($tokenDetails);
            Mail::to($input['email'])->queue((new UserEmails($input))->emailVerificationEmail($input['token']));

            $mainData = [];
            $text = APIUserResponse::$OTPSentViaMail;
            return $this->respondOK($mainData, $text);

        }catch(QueryException $e){
            return $this->handleQueryException($e);
        }catch(\Exception $e){
            return $this->handleException($e);
        }


    }

    public function resendPhoneOTP(Request $request){
        $input = $request->only(
            'phoneno'
        );
        $validator = Validator::make($input, [
                "phoneno"=> "required|regex:/^\+?[0-9]\d{1,20}$/",
            ],
            $messages =[
                "phoneno.regex"=> "Invalid phone number",
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
            $user = $this->userRepository->getUser("phoneno",$input['phoneno']);
            if(!$user){
                $text = APIUserResponse::$userEmailNotExist;
                $mainData= [];
                $hint = ["DB server Error","Ensure to use the method stated in the documentation."];
                $linktosolve = "https://";
                $errorCode = APIErrorCode::$internalUserWarning;
                return $this->respondInternalError($mainData, $text, $hint, $linktosolve, $errorCode);
            }

            //detlete user token
            $this->userTokenRepository->deleteAllUserToken($input['phoneno']);

            //create another token
            $otp = UtilityFunctions::generateUniqueNumericKey("user_tokens", "token", 4);

            // create verification token details
            $tokenDetails = [
                'userid' => $user['userid'],
                'user_identity' => $input['phoneno'],
                'identity_type' => 2, //1-Email 2-Phone
                'token' => $otp,
                'token_type' => 1, //1-Verify 2-Reset
                'expire_at' => date('Y-m-d H:i:s', strtotime('+5 minutes'))
            ];

            //add token to db
            $this->userTokenRepository->addUserToken($tokenDetails);
            Mail::to($input['email'])->queue((new UserEmails($input))->emailVerificationEmail($input['token']));

            $mainData = [];
            $text = APIUserResponse::$forgotPasswordOTP;
            return $this->respondOK($mainData, $text);

        }catch(QueryException $e){
            return $this->handleQueryException($e);
        }catch(\Exception $e){
            return $this->handleException($e);
        }


    }

    public function setPin(Request $request){
        $input = $request->only(['pin']);

        $validator = Validator::make($input, [
                'pin' => 'required|integer|between:0000,9999',
            ],
            $messages =[
                'pin.between' => 'Invalid pin.',
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
            $input['pin']= bcrypt($input['pin']);
            $userid = Auth::user()->userid;
            $this->userRepository->setPin($userid, $input['pin']);

            $mainData = [];
            $text = APIUserResponse::$pinSet;
            return $this->respondOK($mainData, $text);

        }catch(QueryException $e){
            return $this->handleQueryException($e);
        }catch(\Exception $e){
            return $this->handleException($e);
        }

    }

    public function uploadProfilePic(Request $request){
        //upload image only
        $input = $request->only(['image']);
        $validator = Validator::make($input, [
            // image input must be an image format
                'image'=> 'required|image|mimes:jpg,png,jpeg,gif,svg|max:2048',
            ],
            // $messages =[
            //     'image' => 'Invalid image.',
            // ]
        );
        if ($validator->fails()) {
            $text = APIUserResponse::$respondValidationError;
            $mainData= [];
            $hint = $validator->errors()->all();
            $linktosolve = 'https//:';
            $errorCode = APIErrorCode::$internalUserWarning;
            return $this->respondValidationError($mainData, $text, $hint, $linktosolve, $errorCode);
        }

        try{
            //check if its a valid image 


        }catch(QueryException $e){
            return $this->handleQueryException($e);
        }catch(\Exception $e){
            return $this->handleException($e);
        }


    }

    public function uploadImage(Request $request)
    {
        $input = $request->only(['image']);
        $validator = Validator::make($request->all(), [
            'image' => 'required|image:jpeg,png,jpg,gif,svg|max:2048'
        ]);

        if ($validator->fails()) {
            $text = APIUserResponse::$respondValidationError;
            $mainData= [];
            $hint = $validator->errors()->all();
            $linktosolve = 'https//:';
            $errorCode = APIErrorCode::$internalUserWarning;
            return $this->respondValidationError($mainData, $text, $hint, $linktosolve, $errorCode);
        }
        try{
            $uploadFolder = 'profiles';
            $image = $request->file('image');
            $image_uploaded_path = $image->store($uploadFolder, 'public/images');
            // $uploadedImageResponse = array(
            //     "image_name" => basename($image_uploaded_path),
            //     "image_url" => Storage::disk('public')->u,
            //     "mime" => $image->getClientMimeType()
            // );
            $mainData = [];
            $text = APIUserResponse::$imageUploadedSuccesful;
            return $this->respondOK($mainData, $text);

        }catch(QueryException $e){
            return $this->handleQueryException($e);
        }catch(\Exception $e){
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

    protected function handleQueryException(QueryException $e){
        $method = request()->method();
        $errorMessages = [
            'POST' => APIUserResponse::$dbInsertError,
            'GET' => APIUserResponse::$dbQueryError,
            'PUT' => APIUserResponse::$dbUpdatingError,
            'PATCH' => APIUserResponse::$dbUpdatingError,
            'DELETE' => APIUserResponse::$deletingError,
        ];

        // Default error message in case of an unknown method
        $defaultErrorMessage = APIUserResponse::$dbOperationError;
        $text = $errorMessages[$method] ?? $defaultErrorMessage;
        $errorInfo = $e->errorInfo;
        $mainData= [];
        $hint = ["Ensure to use the method stated in the documentation."];
        $linktosolve = "https://";
        $errorCode = APIErrorCode::$internalInsertDBFatal;
        return $this->respondInternalError($mainData, $text, $errorInfo, $linktosolve, $errorCode);
    }

}
