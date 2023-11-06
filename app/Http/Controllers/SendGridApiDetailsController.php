<?php

namespace App\Http\Controllers;

use App\Config\APIErrorCode;
use App\Config\APIUserResponse;
use App\Interfaces\SendGridInterface;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SendGridApiDetailsController extends BaseController
{
    private SendGridInterface $sendGridRepository;
    public function __construct(SendGridInterface $sendGridRepository){
        $this->sendGridRepository = $sendGridRepository;
    }
    /**
     * Display a listing of the resource.
     */
    public function getSendgrid(Request $request){
        try{ 
            $allData = $this->sendGridRepository->getAllSendgrid();
            $text = ($allData->count() > 0) ? APIUserResponse::$getRequestFetched: APIUserResponse::$getRequestNoRecords;
            $mainData = $allData;
            return $this->respondOK($mainData, $text);

        } catch(QueryException $e){
            return $this->handleQueryException($e);
        }catch(\Exception $e){
            return $this->handleException($e);
        }
    }

    public function getSendgridByid(String $id)
    {
        try{ 
            $allData = $this->sendGridRepository->getSendGridByid($id);

            $text = (!empty($allData)) ? APIUserResponse::$getRequestFetched: APIUserResponse::$getRequestNoRecords;
            $mainData = $allData;
            return $this->respondOK($mainData, $text);

        } catch(QueryException $e){
            return $this->handleQueryException($e);
        }catch(\Exception $e){
            return $this->handleException($e);
        }
    }

    public function addSendGrid(Request $request){
        $input = $request->only([
            'name', 'secretid', 'apikey', 'emailfrom'
        ]);
        //validate input
        $validator = Validator::make($input, [
                'name' => 'required|string',
                'secretid' => 'required|string',
                'apikey' => 'required|string',
                'emailfrom' => 'required|string|email',
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
            $new = $this->sendGridRepository->addSendGridApi($input);
            $text = APIUserResponse::$addSendGrid;
            $mainData = [];
            return $this->respondOK($mainData, $text);

        }catch(QueryException $e){
            return $this->handleQueryException($e);
        }catch(\Exception $e){
            return $this->handleException($e);
        }

    }

    public function updateSendGrid(Request $request){
        $input = $request->only([
            'name', 'secretid', 'apikey', 'emailfrom', 'id'
        ]);
        //validate input
        $validator = Validator::make($input, [
                'id'=> 'reequired|integer',
                'name' => 'required|string',
                'secretid' => 'required|string',
                'apikey' => 'required|string',
                'emailfrom' => 'required|string|email',
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
            $isExist = $this->sendGridRepository->checkIfExist($input['id']);
            if(!$isExist){
                // admin not found
                $text = APIUserResponse::$invalidSendGridid;
                $mainData= [];
                $hint = ["Ensure to use the method stated in the documentation.",'Pass in valid sendgrid id.'];
                $linktosolve = "https://";
                $errorCode = APIErrorCode::$internalUserWarning;
                return $this->respondBadRequest($mainData, $text, $hint, $linktosolve, $errorCode);
            }
            $new = $this->sendGridRepository->updateSendGrid($input);
            $text = APIUserResponse::$updateSendGrid;
            $mainData = [];
            return $this->respondOK($mainData, $text);

        }catch(QueryException $e){
            return $this->handleQueryException($e);
        }catch(\Exception $e){
            return $this->handleException($e);
        }

    }

    public function changeSendGridStatus(Request $request){
        $input = $request->only([
            'id', 'status'
        ]);
        //validate input
        $validator = Validator::make($input, [
                "id"=> "required|integer|exist:send_grid_api_details,id",
                'status' => 'required|in:1,0',
            ],
            $messages =[
                'id.exist' => 'Sendgride id not found.',
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

            $id = $input['id'];
            $status = $input['status'];
            $isExist = $this->sendGridRepository->checkIfExist($id);
            if(!$isExist){
                // admin not found
                $text = APIUserResponse::$invalidSendGridid;
                $mainData= [];
                $hint = ["Ensure to use the method stated in the documentation.",'Pass in valid sendgrid id.'];
                $linktosolve = "https://";
                $errorCode = APIErrorCode::$internalUserWarning;
                return $this->respondBadRequest($mainData, $text, $hint, $linktosolve, $errorCode);
            }
            $newAdmin = $this->sendGridRepository->changeStatus($id, $status);
            $text = APIUserResponse::$statusChangedMessage;
            $mainData = [];
            return $this->respondOK($mainData, $text);
        } catch(QueryException $e){
            return $this->handleQueryException($e);
        }catch(\Exception $e){
            return $this->handleException($e);
        }
    }

    public function delete(String $id){
        try {
            $isExist = $this->sendGridRepository->checkIfExist($id);
            if($isExist){
                // admin not found
                $text = APIUserResponse::$invalidSendGridid;
                $mainData= [];
                $hint = ["Ensure to use the method stated in the documentation.",'Pass in valid sendgrid id.'];
                $linktosolve = "https://";
                $errorCode = APIErrorCode::$internalUserWarning;
                return $this->respondBadRequest($mainData, $text, $hint, $linktosolve, $errorCode);
            }
            $newAdmin = $this->sendGridRepository->deleteSendGrid($id);
            $text = APIUserResponse::$deleteSendGrid;
            $mainData = [];
            return $this->respondOK($mainData, $text);
        } catch(QueryException $e){
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
