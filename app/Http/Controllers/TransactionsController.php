<?php

namespace App\Http\Controllers;

use App\Config\APIErrorCode;
use App\Config\APIUserResponse;
use App\Models\Transactions;
use App\Http\Controllers\Controller;
use App\Interfaces\UserTransactionInterface;
use App\Interfaces\UserWalletInterface;
use App\Utilities\UtilityFunctions;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use PhpParser\Node\Expr\Cast\String_;

class TransactionsController extends BaseController
{
    private UserTransactionInterface $userTransactionRepository;
    private UserWalletInterface $userWalletRepository;
    public function __construct(UserTransactionInterface $userTransactionRepository, UserWalletInterface $userWalletRepository){
        $this->userTransactionRepository = $userTransactionRepository;
        $this->userWalletRepository = $userWalletRepository;
    }
    /**
     * Display a listing of the resource.
     */
    public function getMyTransaction(Request $request, String $id){
        //
        try {

            //check if id is passed, get transaction with id
            $userid = Auth::user()->userid;
            if(!empty($id)){
                $transactions = $this->userTransactionRepository->getUserTransactionByid($userid, $id);
            }else{
                $transactions = $this->userTransactionRepository->getMytransaction($userid);
            }
            $returnData = [];

            if($transactions->count() > 0){
                foreach ($transactions as $transaction) {
                    $transactionDate = $transaction->created_at->toDateString();
                    if (!array_key_exists($transactionDate, $returnData)) {
                        $returnData[$transactionDate] = [
                            'transaction_date' => $transaction->created_at->format('d m Y'),
                            'transactions' => [],
                        ];
                    }
                    $returnData[$transactionDate]['transactions'][] = $transaction;
                }

                // Convert the associative array to indexed array
                $returnData = array_values($returnData);
            }

            $text = (count($transactions) > 0)? APIUserResponse::$getRequestNoRecords : APIUserResponse::$getRequestNoRecords;
            return $this->respondOK($returnData, $text);

        }catch(QueryException $e){
            return $this->handleQueryException($e);
        }catch(\Exception $e){
            return $this->handleException($e);
        }

    }

    //for admin
    public function getTransactions(String $trnasactionid){
        try {
            if(!empty($trnasactionid)){
                $transactions = $this->userTransactionRepository->getTransaction($trnasactionid);
            }else{
                $transactions = $this->userTransactionRepository->getAllTransactions();
            }

            $text = (count($transactions) > 0)? APIUserResponse::$getRequestFetched : APIUserResponse::$getRequestNoRecords;
            return $this->respondOK($transactions, $text);
        } catch(QueryException $e){
            return $this->handleQueryException($e);
        }catch(\Exception $e){
            return $this->handleException($e);
        }
    }


    //1-Fund, 2-Swap Usdt-Naira, 3-Send, 4-Withdraw
    public function newTransaction(Request $request, String_ $type){
        //SELECT `id`, `transactionid`, `transaction_type`, `amount_crypto`, `amount_fiat`, `status`, `userid`, `charges`, `userbankid`, `created_at`, `updated_at`, `walletid` FROM `transactions` WHERE 1
        $input = $request->only(
            "transaction_type",
            "amount",
            "userbankid",
            "walletid",
        );
        // Validate the request data using the rules specified in UserBankAccountRequest
        $validator = Validator::make($input, [
                'transaction_type' => 'required|in:1,2,3,4',
                "walletid" => "required|string|exists:user_wallets,wallettrackid",
                "amount"=> "required|numeric",

            ],
            $messages =[
                'transaction_type.in' => 'Invalid transaction type',
                'walletid.exist'=> 'Invalid user wallet',
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
            $walletid = $input["walletid"];
            //validate userwallet
            //userid	currencytag	wallettrackid	walletbal
            $userWalletData =$this->userWalletRepository->getWalletData("wallettrackid", $walletid, ["userid", "walletbal", "wallettrackid", "currencytag"]);

            //check if $userwalltData is not empty
            if(empty($userWalletData) ){
                $text = APIUserResponse::$invalidWalletid;
                $mainData= [];
                $hint = ["Ensure to use the method stated in the documentation.",'Pass in valid walletid.'];
                $linktosolve = "https://";
                $errorCode = APIErrorCode::$internalUserWarning;
                return $this->respondBadRequest($mainData, $text, $hint, $linktosolve, $errorCode);
            }

            if($userWalletData["userid"] != $userid){
                $text = APIUserResponse::$invalidWalletid;
                $mainData= [];
                $hint = ['Pass in valid walletid.',"Wallet does not belong to user","Ensure to use the method stated in the documentation."];
                $linktosolve = "https://";
                $errorCode = APIErrorCode::$internalUserWarning;
                return $this->respondBadRequest($mainData, $text, $hint, $linktosolve, $errorCode);
            }

            $userBalance = $userWalletData['walletbal'];
            //check if user has sufficient ballance
            if($input['amount'] < $userBalance){
                $text = APIUserResponse::$insufficientUserBalance;
                $mainData= [];
                $hint = ['Pass in valid walletid.',"Wallet does not belong to user","Ensure to use the method stated in the documentation."];
                $linktosolve = "https://";
                $errorCode = APIErrorCode::$internalUserWarning;
                return $this->respondBadRequest($mainData, $text, $hint, $linktosolve, $errorCode);
            }


            $input['transactionid'] = UtilityFunctions::generateUniqueShortKey('transactions', 'transactionid');
            $input['userid'] = $userid;
            $input['status'] = 0;
            $newtranasction = $this->userTransactionRepository->newTransaction($input);
            $text =  APIUserResponse::$newTransaction;
            $mainData = [];
            return $this->respondOK($mainData, $text);

        }catch(QueryException $e){
            return $this->handleQueryException($e);
        }catch(\Exception $e){
            return $this->handleException($e);
        }
    }

    public function fund($transactionDetails){
        try{

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
