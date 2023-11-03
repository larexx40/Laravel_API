<?php

namespace App\Http\Controllers;

use App\Config\APIErrorCode;
use App\Config\APIUserResponse;
use App\Http\Controllers\Controller;
use App\Interfaces\UserWalletInterface;
use App\Utilities\UtilityFunctions;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserWalletController extends BaseController
{

    private UserWalletInterface $userWalletRepository;
    public function __construct(UserWalletInterface $userWalletRepository){
        $this->userWalletRepository = $userWalletRepository;
    }
    /**
     * Display a listing of the resource.
     */
    public function getAllUserWallet()
    {
        //
        try{
            $wallets =  $this->userWalletRepository->getAllUserWallet();
            $text = (count($wallets) > 0)? APIUserResponse::$getRequestNoRecords : APIUserResponse::$getRequestNoRecords;
            unset($wallets['updated_at']);
            return $this->respondOK($wallets, $text);
        }catch(QueryException $e){
            return $this->handleQueryException($e);
        }catch(\Exception $e){
            return $this->handleException($e);
        }

    }

    public function getMyWallet()
    {
        //
        try{
            $userid = Auth::user()->userid;
            $wallets =  $this->userWalletRepository->getUserWalletByUserid($userid);
            $text = (count($wallets) > 0)? APIUserResponse::$getRequestNoRecords : APIUserResponse::$getRequestNoRecords;
            unset($wallets['updated_at']);
            return $this->respondOK($wallets, $text);
        }catch(QueryException $e){
            return $this->handleQueryException($e);
        }catch(\Exception $e){
            return $this->handleException($e);
        }

    }

    public function getUserWallet(String $userid)
    {
        //
        try{
            // $admin =Auth
            $isExist = UtilityFunctions::checkIfExist("users", "userid", $userid);
            if(!$isExist){
                $text = APIUserResponse::$invalidUserid;
                $mainData= [];
                $hint = ["Ensure to use the method stated in the documentation.",'Pass in valid user id.'];
                $linktosolve = "https://";
                $errorCode = APIErrorCode::$internalUserWarning;
                return $this->respondBadRequest($mainData, $text, $hint, $linktosolve, $errorCode);
            }
            $wallets =  $this->userWalletRepository->getUserWalletByUserid($userid);
            $text = (count($wallets) > 0)? APIUserResponse::$getRequestNoRecords : APIUserResponse::$getRequestNoRecords;
            unset($wallets['updated_at']);
            return $this->respondOK($wallets, $text);
        }catch(QueryException $e){
            return $this->handleQueryException($e);
        }catch(\Exception $e){
            return $this->handleException($e);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function addNewWallet(Request $request){
        //name	image_link	sysbankcode	paystackbankcode	monifybankcode	shbankcodes
        $input = $request->only([
            'currencytag'
        ]);
        //validate input
        $validator = Validator::make($input, [
                'curencytag'=>'required|string|exists:currencysystems,curencytag',
            ],
            $messages =[
                'curencytag.exists' => 'Currncy system with id does not exist.',
            ]
        );

        if ($validator->fails()) {
            $text = APIUserResponse::$respondValidationError;
            $mainData= [];
            $hint = $validator->errors()->all();
            $linktosolve = "https://";
            $errorCode = APIErrorCode::$internalUserWarning;
            return $this->respondValidationError($mainData, $text, $hint, $linktosolve, $errorCode);
        };

        try {
            $isExist = $this->userWalletRepository->checkIfExist('phoneno', $input['phoneno']);
            if($isExist){
                $text = APIUserResponse::$phoneExist;
                $mainData= [];
                $hint = ['There is a user with phone number in DB' ,"Ensure to use the method stated in the documentation."];
                $linktosolve = "https://";
                $errorCode = APIErrorCode::$internalUserWarning;
                return $this->respondBadRequest($mainData, $text, $hint, $linktosolve, $errorCode);
            }
            $input['wallettrackid']= UtilityFunctions::generateUniqueShortKey("userwallet", "wallettrackid");
            $input["walletbal"]= "0.00";
            $input["walletpendbal"]= "0.00";
            $input["walletescrowbal"]= "0.00";
            $wallet = $this->userWalletRepository->addNewUserWallet($input);
            $text = APIUserResponse::$newWalletAdded;
            $mainData = [];
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
