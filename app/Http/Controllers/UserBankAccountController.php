<?php

namespace App\Http\Controllers;

use App\Config\APIErrorCode;
use App\Config\APIUserResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserBankAccountResource;
use App\Interfaces\UserBankInterface;
use App\Models\UserBankAccount;
use App\Utilities\UtilityFunctions;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserBankAccountController extends BaseController
{

    private UserBankInterface $userBankRepository;
    public function __construct(UserBankInterface $userBankRepository)
    {
        $this->userBankRepository = $userBankRepository;
        
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request){
        try {
            $userid = Auth::user()->userid;
            $bankAccounts = $this->userBankRepository->getUserBankByUserid($userid);
            $text = (count($bankAccounts) > 0)? APIUserResponse::$getRequestFetched : APIUserResponse::$getRequestNoRecords;
            // $bankAccounts = UserBankAccountResource::collection($bankAccounts);
            return $this->respondOK($bankAccounts, $text);
        } catch(\Exception $e){
            return $this->handleException($e);
        }
        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function addBank(Request $request)
    {


        $input = $request->only(
            "account_no",
            "account_name",
            "sys_bank_id",
        );
        // Validate the request data using the rules specified in UserBankAccountRequest
        $validator = Validator::make($input, [
                "account_no" => "required|regex:/^[0-9]{10,16}$/",
                "account_name" => "required",
                "sys_bank_id" => "required",
                
            ],
            $messages =[
                'sys_bank_id.required' => 'Select your bank',
                'account_no.required' => 'Pass your account number',
                'account_no.regex' => 'Invalid account number',
                'account_name.required' => 'Pass your account name',
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

        $input['bankid'] = UtilityFunctions::generateUniqueShortKey('user_bank_accounts', 'bankid');
        $input['status'] = 1;
        $input['is_default'] = 0;

        try{
            $userid = Auth::user()->userid;
            $input['user_id'] = $userid;
            $user = $this->userBankRepository->createUserBank($input);
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
