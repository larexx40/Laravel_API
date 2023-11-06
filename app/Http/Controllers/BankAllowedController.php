<?php

namespace App\Http\Controllers;

use App\Config\APIErrorCode;
use App\Config\APIUserResponse;
use App\Http\Controllers\Controller;
use App\Interfaces\BankAllowedInterface;
use App\Utilities\UtilityFunctions;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class BankAllowedController extends BaseController
{

    private BankAllowedInterface $bankAllowedRepository;
    public function __construct(BankAllowedInterface $bankAllowedRepository){
        $this->bankAllowedRepository = $bankAllowedRepository;
    }
    /**
     * Display a listing of the resource.
     */
    public function getAllBanks(Request $request)
    {
        // filter, search and pagination
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search');
        $filter = $request->input('filter');
        try{
            $banks =  $this->bankAllowedRepository->getAllBanks($perPage, $search, $filter);
            $text = (count($banks) > 0)? APIUserResponse::$getRequestFetched : APIUserResponse::$getRequestNoRecords;;
            return $this->respondOK($banks, $text);
        }catch(QueryException $e){
            return $this->handleQueryException($e);
        }catch(\Exception $e){
            return $this->handleException($e);
        }

    }

    /**
     * Show the form for creating a new resource.
     */
    public function addBank(Request $request)
    {
        //name	image_link	sysbankcode	paystackbankcode	monifybankcode	shbankcodes
        $input = $request->only([
            'name', 'image_link', 'paystackbankcode', 'monifybankcode',"shbankcodes"
        ]);
        //validate input
        $validator = Validator::make($input, [
                'name' => 'required',
                'image_link' => 'required|url',
                'paystackbankcode' => 'required|string',
                'monifybankcode' => 'required|string',
                'shbankcodes'=> 'required|string'
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
            $input['sysbankcode']= UtilityFunctions::generateUniqueShortKey("bank_alloweds", "sysbankcode");
            $newBank = $this->bankAllowedRepository->addNewBank($input);
            $text = APIUserResponse::$addBankAccount;
            $mainData = [];
            return $this->respondOK($mainData, $text);

        }catch(QueryException $e){
            return $this->handleQueryException($e);
        }catch(\Exception $e){
            return $this->handleException($e);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function updateBank(Request $request)
    {
        //
        $input = $request->only([
            'name', 'image_link', 'sysbankcode', 'paystackbankcode', 'monifybankcode',"shbankcodes"
        ]);
        //validate input
        $validator = Validator::make($input, [
                'name' => 'required',
                'image_link' => 'required|url',
                'paystackbankcode' => 'required|string',
                'monifybankcode' => 'required|string',
                'shbankcodes'=> 'required|string',
                "sysbankcode"=> "required|exist:bank_alloweds,sysbankcode"
            ],
            $messages =[
                'sysbankcode.exist' => 'Bank code id not found.',
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
            $newAdmin = $this->bankAllowedRepository->updateBank($input);
            $text = APIUserResponse::$updateBankAccount;
            $mainData = [];
            return $this->respondOK($mainData, $text);

        }catch(QueryException $e){
            return $this->handleQueryException($e);
        }catch(\Exception $e){
            return $this->handleException($e);
        }


    }

    public function changeBankStatus(Request $request)
    {
        //
        $input = $request->only([
            'sysbankcode', 'status'
        ]);
        //validate input
        $validator = Validator::make($input, [
                "sysbankcode"=> "required|exist:bank_alloweds,sysbankcode",
                'status' => 'required|in:1,0',
            ],
            $messages =[
                'sysbankcode.exist' => 'Bank code id not found.',
                'status.in'=>"Stantus can only be 1 or 0"
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

            $bankid = $input['sysbankcode'];
            $status = $input['status'];
            $bank = $this->bankAllowedRepository->getBankData("sysbankcode", $bankid, ["sysbankcode", "name"]);
            if(empty($bank)){
                // admin not found
                $text = APIUserResponse::$invalidBankId;
                $mainData= [];
                $hint = ["Ensure to use the method stated in the documentation.",'Pass in valid bank id.'];
                $linktosolve = "https://";
                $errorCode = APIErrorCode::$internalUserWarning;
                return $this->respondBadRequest($mainData, $text, $hint, $linktosolve, $errorCode);
            }
            $newAdmin = $this->bankAllowedRepository->changeBankStatus($bankid, $status);
            $text = APIUserResponse::$statusChangedMessage;
            $mainData = [];
            return $this->respondOK($mainData, $text);

        }catch(QueryException $e){
            return $this->handleQueryException($e);
        }catch(\Exception $e){
            return $this->handleException($e);
        }


    }

    /**
     * Show the form for editing the specified resource.
     */
    public function deleteBank(String $bankid)
    {
        // find admin by adminid
        try {
            $bank = $this->bankAllowedRepository->getBankData("sysbankcode", $bankid, ["sysbankcode", "name"]);
            if(empty($bank)){
                // admin not found
                $text = APIUserResponse::$invalidBankId;
                $mainData= [];
                $hint = ["Ensure to use the method stated in the documentation.",'Pass in valid bank id.'];
                $linktosolve = "https://";
                $errorCode = APIErrorCode::$internalUserWarning;
                return $this->respondBadRequest($mainData, $text, $hint, $linktosolve, $errorCode);
            }
            //delete admin
            $delete = $this->bankAllowedRepository->deleteBank($bankid);
            $text = APIUserResponse::$deleteBank ;
            $mainData= [];
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
