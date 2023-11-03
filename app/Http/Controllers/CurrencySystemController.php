<?php

namespace App\Http\Controllers;

use App\Config\APIErrorCode;
use App\Config\APIUserResponse;
use App\Http\Controllers\Controller;
use App\Interfaces\CurrencySystemInterface;
use App\Utilities\UtilityFunctions;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CurrencySystemController extends BaseController
{

    private CurrencySystemInterface $currencySystemRepository;
    public function __construct(CurrencySystemInterface $currencySystemRepository){
        $this->currencySystemRepository = $currencySystemRepository;
    }
    /**
     * Display a listing of the resource.
     */
    public function getAllCurrencySystem()
    {
        //
        try{
            $currencySystem =  $this->currencySystemRepository->getAllCurrencySystem();
            $text = (count($currencySystem) > 0)? APIUserResponse::$getRequestNoRecords : APIUserResponse::$getRequestNoRecords;
            $currencySystem['currency_status_value'] = $currencySystem['status'] == 1 ? "Active" : "Inactive";
            unset($currencySystem['updated_at']);
            return $this->respondOK($currencySystem, $text);
        }catch(QueryException $e){
            return $this->handleQueryException($e);
        }catch(\Exception $e){
            return $this->handleException($e);
        }

    }

    /**
     * Show the form for creating a new resource.
     *   `name` varchar(1000) NOT NULL,
     */
    public function addCurrencySytem(Request $request)
    {
        $input = $request->only([
            'name', 'sign', 'sidebarname',"imglink", "maxsendamtauto", 'nametag'
        ]);
        //validate input
        $validator = Validator::make($input, [
                'name' => 'required|string',
                'nametag'=>'required|string',
                'image_link' => 'required|url',
                'sign' => 'required|string',
                'sidebarname'=> 'required|string',
                "maxsendamtauto"=> 'required|decimal'
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
            $input['currencytag']= UtilityFunctions::generateUniqueShortKey("currencysystems", "currencytag");
            $input['currency_status'] =0;
            $input['activatesend'] =0;
            $input['activatereceive'] =0;
            $input['defaultforusers'] =0;
            $new = $this->currencySystemRepository->addCurrencySystem($input);
            $text = APIUserResponse::$addCurrencySystem;
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
    public function updateCurrencySystem(Request $request)
    {
        //
        $input = $request->only([
            'name', 'sign', 'sidebarname',"imglink", "maxsendamtauto", 'nametag'
        ]);
        //validate input
        $validator = Validator::make($input, [
                'name' => 'required|string',
                'nametag'=>'required|string',
                'image_link' => 'required|url',
                'sign' => 'required|string',
                'sidebarname'=> 'required|string',
                "maxsendamtauto"=> 'required|decimal',
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
        }

        try {
            $new = $this->currencySystemRepository->updateCurrencySystem($input);
            $text = APIUserResponse::$updateCurrencySystem;
            $mainData = [];
            return $this->respondOK($mainData, $text);

        }catch(QueryException $e){
            return $this->handleQueryException($e);
        }catch(\Exception $e){
            return $this->handleException($e);
        }


    }

    public function changeCurrencySystemStatus(Request $request)
    {
        //
        $input = $request->only([
            'sysbankcode', 'status'
        ]);
        //validate input
        $validator = Validator::make($input, [
                'status' => 'required|in:1,0',
                'curencytag'=>'required|string|exists:currencysystems,curencytag',
            ],
            $messages =[
                'curencytag.exists' => 'Currncy system with id does not exist.',
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

            $curencytag = $input['curencytag'];
            $status = $input['status'];
            $currncySystem = $this->currencySystemRepository->getCurrencySystemData("curencytag", $curencytag, ["curencytag", "name"]);
            if(empty($currncySystem)){
                // admin not found
                $text = APIUserResponse::$invalidCurrencyId;
                $mainData= [];
                $hint = ["Ensure to use the method stated in the documentation.",'Pass in valid currency id.'];
                $linktosolve = "https://";
                $errorCode = APIErrorCode::$internalUserWarning;
                return $this->respondBadRequest($mainData, $text, $hint, $linktosolve, $errorCode);
            }
            $newcurrency = $this->currencySystemRepository->changeCurrencySystemStatus($curencytag, $status);
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
    public function deleteCurrencySystem(String $curencytag)
    {
        // find admin by adminid
        try {
            $currencySystem = $this->currencySystemRepository->getCurrencySystemData("curencytag", $curencytag, ["curencytag", "name"]);
            if(empty($currencySystem)){
                // admin not found
                $text = APIUserResponse::$invalidCurrencyId;
                $mainData= [];
                $hint = ["Ensure to use the method stated in the documentation.",'Pass in valid currency system id.'];
                $linktosolve = "https://";
                $errorCode = APIErrorCode::$internalUserWarning;
                return $this->respondBadRequest($mainData, $text, $hint, $linktosolve, $errorCode);
            }
            //delete admin
            $delete = $this->currencySystemRepository->deleteCurrencySystem($curencytag);
            $text = APIUserResponse::$deleteCurrencySystem;
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
