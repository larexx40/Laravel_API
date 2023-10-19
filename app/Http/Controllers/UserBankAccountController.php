<?php

namespace App\Http\Controllers;

use App\Config\APIErrorCode;
use App\Config\APIUserResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserBankAccountRequest;
use App\Http\Resources\APIPaginateCollection;
use App\Http\Resources\UserBankAccountResource;
use App\Models\UserBankAccount;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class UserBankAccountController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $bankAccounts = UserBankAccount::all();
        $text = "Data fetched successfully";
        if(count($bankAccounts) == 0){
            $text = "No record found";
        }
        $bankAccounts = UserBankAccountResource::collection($bankAccounts);
        return $this->respondOK($bankAccounts, $text);
        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserBankAccountRequest $request)
    {

        $input = $request->only(
            "bankid",
            "user_id",
            "bank_name",
            "account_no",
            "account_name",
            "sys_bank_id",
        );
        // Validate the request data using the rules specified in UserBankAccountRequest
        $validator = Validator::make($input, [
            "bankid" => "required",
            "user_id" => "required",
            "bank_name" => "required",
            "account_no" => "required",
            "account_name" => "required",
            "sys_bank_id" => "required",
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        try{
            UserBankAccount::create($input);
            $text = APIUserResponse::$addBankAccount;
            $mainData = [];
            return $this->respondOK($mainData, $text);

        }catch(QueryException $e){
            $errorInfo = $e->errorInfo;
            $text = APIUserResponse::$dbInsertError;
            $mainData= [];
            $hint = ["Ensure to use the method stated in the documentation."];
            $linktosolve = "https://";
            $errorCode = APIErrorCode::$internalInsertDBFatal;
            return $this->respondInternalError($mainData, $text, $errorInfo, $linktosolve, $errorCode);
        }

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            //get bankaccount with particular id
            $bankAccount = UserBankAccount::findOrFail($id);        
            $text = APIUserResponse::$getRequestFetched;
            $mainData = $bankAccount;
            return $this->respondOK($mainData, $text);
        } catch (Exception $e) {  
            $text = APIUserResponse::$getRequestNoRecords;
            $mainData = [];
            return $this->respondOK($mainData, $text);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //update bankaccount with a partivular id
        $bankAccount = UserBankAccount::findOrFail($id);
        $bankAccount->update($request->only(
            "user_id",
            "bank_name",
            "account_no",
            "account_name",
        ));
        return response()->json([
            "success" => true,
            "message" => "Bank account updated successfully"
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //delete bank account
        $bankAccount = UserBankAccount::findOrFail($id);
        $bankAccount->delete();
        return response()->json([
            "success" => true,
            "message" => "Bank account deleted successfully"
        ]);
    }
}
