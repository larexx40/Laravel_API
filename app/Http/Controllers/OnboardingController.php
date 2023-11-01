<?php

namespace App\Http\Controllers;

use App\Config\APIErrorCode;
use App\Config\APIUserResponse;
use App\Http\Controllers\Controller;
use App\Interfaces\UserRepositoryInterface;
use App\Interfaces\UserTokenInterface;
use App\Mail\UserEmails;
use App\Services\SmsServices;
use App\Utilities\UtilityFunctions;
use App\Utilities\ExternalCalls;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class OnboardingController extends BaseController
{
    //
    private UserRepositoryInterface $userRepository;
    private UserTokenInterface $userTokenRepository;
    private SmsServices $smsService;
    public function __construct(UserRepositoryInterface $userRepository, UserTokenInterface $userTokenRepository, SmsServices $smsService){
        $this->userRepository = $userRepository;
        $this->userTokenRepository = $userTokenRepository;
        $this->smsService = $smsService;
    }

    public function registerEmail(Request $request){
        $input = $request->only(
            "email",
        );
        $validator = Validator::make($input, [
                'email' => 'required|string|email|unique:users,email',
            ],
            $messages =[
                'email.unique' => 'Email already exists.',
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
            $isExist = $this->userRepository->checkIfUserExist("email", $input['email']);
            if($isExist){
                $text = APIUserResponse::$emailExist;
                $mainData= [];
                $hint = ['There is a user with email in DB' ,"Ensure to use the method stated in the documentation."];
                $linktosolve = "https://";
                $errorCode = APIErrorCode::$internalUserWarning;
                return $this->respondBadRequest($mainData, $text, $hint, $linktosolve, $errorCode);
            }

            //create otp
            $otp = UtilityFunctions::generateUniqueNumericKey("user_tokens", "token", 4);
            // create verification token details
            $tokenDetails = [
                'user_identity' => $input['email'],
                'identity_type' => 1, //1-Email 2-Phone
                'token' => $otp,
                'token_type' => 1, //1-Verify 2-Reset
                'expire_at' => date('Y-m-d H:i:s', strtotime('+5 minutes'))
            ];
            //add token to db
            $this->userTokenRepository->addUserToken($tokenDetails);
            $user = (object) [
                'name' => "Olanrewaju",
                'email' => $input['email'],
            ];
            Mail::to($input['email'])->queue((new UserEmails($user))->emailVerificationEmail($otp));

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
                'otp' => 'required|string|max:4',
                'email' => 'required|string|email|unique:users,email',
            ],
            $messages =[
                'email.unique' => 'Email already exists.',
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
            $token = $this->userTokenRepository->getTokenByToken($input['otp']);
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

            $text = APIUserResponse::$successEmail;
            $mainData = [];
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
                "phoneno"=> "required|unique:users,phoneno|regex:/^\+?[0-9]\d{1,20}$/",
            ],
            $messages =[
                "phoneno.regex"=> "Invalid phone number",
                "phoneno.unique"=>"Phone number already exist"
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

            $isExist = $this->userRepository->checkIfUserExist('phoneno', $input['phoneno']);
            if($isExist){
                $text = APIUserResponse::$phoneExist;
                $mainData= [];
                $hint = ['There is a user with phone number in DB' ,"Ensure to use the method stated in the documentation."];
                $linktosolve = "https://";
                $errorCode = APIErrorCode::$internalUserWarning;
                return $this->respondBadRequest($mainData, $text, $hint, $linktosolve, $errorCode);
            }
            //send welcome email
            //create otp
            $otp = UtilityFunctions::generateUniqueNumericKey("user_tokens", "token", 4);

            // create verification token details
            $tokenDetails = [
                'user_identity' => $input['phoneno'],
                'identity_type' => 2, //1-Email 2-Phone
                'token' => $otp,
                'token_type' => 1, //1-Verify 2-Reset
                'expire_at' => date('Y-m-d H:i:s', strtotime('+5 minutes'))
            ];
            //add token to db
            $this->userTokenRepository->addUserToken($tokenDetails);

            //send sms otp to user phone
            $smsTemplate = Config::get('sms_template.phone_verify_otp');
            if (!$smsTemplate) {
                $text = APIUserResponse::$emailExist;
                $mainData= [];
                $hint = ['Unable to send sms' ,"Ensure to use the method stated in the documentation."];
                $linktosolve = "https://";
                $errorCode = APIErrorCode::$internalUserWarning;
                return $this->respondInternalError($mainData, $text, $hint, $linktosolve, $errorCode);
            }
            $message = str_replace("{code}", $otp, $smsTemplate);
            $sendMessage = $this->smsService->sendSMSWithTermi($input['phoneno'], $message);


            $mainData = [];
            $text = APIUserResponse::$OTPSentViaSMS;
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
            $token = $this->userTokenRepository->getTokenByToken($input['otp']);
            if(!$token){
               $text = APIUserResponse::$invalidOTP;
               $mainData= [];
               $hint = ["check your phone SMS for 4 digit OTP","Ensure to use the method stated in the documentation."];
               $linktosolve = "https://";
               $errorCode = APIErrorCode::$internalUserWarning;
               return $this->respondBadRequest($mainData, $text, $hint, $linktosolve, $errorCode);
            }

            //verify
            if($token['token_type'] != 1){
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
                'user_identity' => $input['email'],
                'identity_type' => 1, //1-Email 2-Phone
                'token' => $otp,
                'token_type' => 1, //1-Verify 2-Reset
                'expire_at' => date('Y-m-d H:i:s', strtotime('+5 minutes'))
            ];

            //add token to db
            $this->userTokenRepository->addUserToken($tokenDetails);
            Mail::to($input['email'])->queue((new UserEmails($input))->emailVerificationEmail($otp));

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
                'user_identity' => $input['phoneno'],
                'identity_type' => 2, //1-Email 2-Phone
                'token' => $otp,
                'token_type' => 1, //1-Verify 2-Reset
                'expire_at' => date('Y-m-d H:i:s', strtotime('+5 minutes'))
            ];

            //add token to db
            $this->userTokenRepository->addUserToken($tokenDetails);

            //send sms otp to user phone
            $smsTemplate = Config::get('sms_template.phone_verify_otp');
            if (!$smsTemplate) {
                $text = APIUserResponse::$emailExist;
                $mainData= [];
                $hint = ['Unable to send sms' ,"Ensure to use the method stated in the documentation."];
                $linktosolve = "https://";
                $errorCode = APIErrorCode::$internalUserWarning;
                return $this->respondInternalError($mainData, $text, $hint, $linktosolve, $errorCode);
            }
            $message = str_replace("{code}", $otp, $smsTemplate);
            $sendMessage = $this->smsService->sendSMSWithTermi($input['phoneno'], $message);

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
            //check if request is not an image
            $image = $request->file('image');
            if (!$image->isValid()) {
                $text = APIUserResponse::$invalidImageSent;
                $mainData= [];
                $hint = $validator->errors()->all();
                $linktosolve = 'https//:';
                $errorCode = APIErrorCode::$internalUserWarning;
                return $this->respondValidationError($mainData, $text, $hint, $linktosolve, $errorCode);
            }
            $imageName = uniqid("USDAF-IMG-", true) . '.' . $image->getClientOriginalExtension();
            $imagePath =  $image->storeAs('public/images/profiles', $imageName);
            // Get the URL of the uploaded image
            // $imageUrl = asset('storage/' . $imagePath);
            $url = Storage::url($imagePath);
            $imageUrl = asset('storage/images/profiles/' . $imageName);

            //save imagename to db


            $mainData = [
                "image_name" => $imagePath,
                "image_url" => $imageUrl,
                "url"=> $url,
            ];
            $text = APIUserResponse::$imageUploadedSuccesful;
            return $this->respondOK($mainData, $text);



        }catch(QueryException $e){
            return $this->handleQueryException($e);
        }catch(\Exception $e){
            return $this->handleException($e);
        }


    }

    public function register(Request $request){
        $input = $request->only(
            "fname",
            "lname",
            "email",
            "phoneno",
            "username",
            'pin',
            "password",
            "accountno",
            'system_bank_code',
            'account_name',
            'sex',
        );

        // Validate the request data using the rules specified in UserRequest
        $validator = Validator::make($input, [
                "fname" => "required",
                'email' => 'required|string|email|unique:users,email',
                'password' => 'required|string|min:6|regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{6,}$/',
                'phoneno' => [ 'string', 'regex:/^[0-9]{11}$/'],
                'dob' => ['date', 'before:today'],
                'sex' => 'required|in:male,female',
                'pin' => 'required|integer|between:0000,9999',
                "lname" => "required",
                "username" => "required",
                "phoneno" => "required",
            ],
            $messages =[
                'email.unique' => 'Email already exists.',
                'password.regex' => 'Password must contain at least one uppercase letter, one lowercase letter, one digit, and one special character (#?!@$%^&*-).',
                'phoneno.regex' => 'The phone number must be 11 digits in length and contain only numbers.',
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

        //bycrypt the password
        $input['password'] = bcrypt($input['password']);
        $input['pin']= bcrypt($input['pin']);
        //generate unique userid for user
        $input['userid'] = UtilityFunctions::generateUniqueShortKey("users", "userid");
        $input['userpubkey'] = UtilityFunctions::generateUniquePubKey("users", "userpubkey");
        $input['is_email_verified'] =1;
        $input['is_phone_verified'] = 1;

        try {
            $newUser = $this->userRepository->createUser($input);
            $token = Auth::login($newUser);
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

    public function getRegSummary(Request $request){
        try{
            $userid = Auth::user()->userid;
            $regDetails = $this->userRepository->getRegSummary($userid);
            $regDetails['pin'] = empty($regDetails['pin']) ? false : true;
            $regDetails['profile_pic'] = empty($regDetails['profile_pic']) ? false : true;
            $regDetails['is_email_verified'] = $regDetails['is_email_verified'] == 1 ? true : false;
            $regDetails['is_phone_verified'] = $regDetails['is_phone_verified'] == 1 ? true : false;

            $text = APIUserResponse::$getRegSummary;
            $mainData = $regDetails;
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
