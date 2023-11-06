<?php

namespace App\Http\Controllers;

use App\Config\APIErrorCode;
use App\Config\APIUserResponse;
use App\Interfaces\ZeptoInterface;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ZeptoApiDetailsController extends BaseController
{
    private ZeptoInterface $zeptoRepository;
    public function __construct(ZeptoInterface $zeptoRepository){
        $this->zeptoRepository = $zeptoRepository;
    }
    /**
     * Display a listing of the resource.
     */
    public function getZeptoByid(String $id)
    {
        try{
            $allData = $this->zeptoRepository->getZeptoByid($id);
            $text = (!empty($allData)) ? APIUserResponse::$getRequestFetched: APIUserResponse::$getRequestNoRecords;
            $mainData = $allData;
            return $this->respondOK($mainData, $text);

        } catch(QueryException $e){
            return $this->handleQueryException($e);
        }catch(\Exception $e){
            return $this->handleException($e);
        }
    }

    public function getZepto(Request $request)
    {
        try{
            $allData = $this->zeptoRepository->getAllZepto();
            $text = (!empty($allData)) ? APIUserResponse::$getRequestFetched: APIUserResponse::$getRequestNoRecords;
            $mainData = $allData;
            return $this->respondOK($mainData, $text);

        } catch(QueryException $e){
            return $this->handleQueryException($e);
        }catch(\Exception $e){
            return $this->handleException($e);
        }
    }

    public function addZepto(Request $request){
        $input = $request->only([
            'name', 'apikey', 'emailfrom'
        ]);
        //validate input
        $validator = Validator::make($input, [
                'name' => 'required|string',
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
            $new = $this->zeptoRepository->addZeptoApi($input);
            $text = APIUserResponse::$addZepto;
            $mainData = [];
            return $this->respondOK($mainData, $text);

        }catch(QueryException $e){
            return $this->handleQueryException($e);
        }catch(\Exception $e){
            return $this->handleException($e);
        }

    }

    public function updateZepto(Request $request){
        $input = $request->only([
            'name', 'apikey', 'emailfrom', 'id'
        ]);
        //validate input
        $validator = Validator::make($input, [
                'id'=> 'reequired|integer',
                'name' => 'required|string',
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
            $isExist = $this->zeptoRepository->checkIfExist($input['id']);
            if(!$isExist){
                // admin not found
                $text = APIUserResponse::$invalidZeptoid;
                $mainData= [];
                $hint = ["Ensure to use the method stated in the documentation.",'Pass in valid zepto id.'];
                $linktosolve = "https://";
                $errorCode = APIErrorCode::$internalUserWarning;
                return $this->respondBadRequest($mainData, $text, $hint, $linktosolve, $errorCode);
            }
            $new = $this->zeptoRepository->updateZeptoApi($input);
            $text = APIUserResponse::$updateZepto;
            $mainData = [];
            return $this->respondOK($mainData, $text);

        }catch(QueryException $e){
            return $this->handleQueryException($e);
        }catch(\Exception $e){
            return $this->handleException($e);
        }

    }

    public function changeZeptoStatus(Request $request){
        $input = $request->only([
            'id', 'status'
        ]);
        //validate input
        $validator = Validator::make($input, [
                "id"=> "required|integer|exist:send_grid_api_details,id",
                'status' => 'required|in:1,0',
            ],
            $messages =[
                'id.exist' => 'Zepto with id not found.',
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
            $isExist = $this->zeptoRepository->checkIfExist($id);
            if(!$isExist){
                $text = APIUserResponse::$invalidZeptoid;
                $mainData= [];
                $hint = ["Ensure to use the method stated in the documentation.",'Pass in valid zepto id.'];
                $linktosolve = "https://";
                $errorCode = APIErrorCode::$internalUserWarning;
                return $this->respondBadRequest($mainData, $text, $hint, $linktosolve, $errorCode);
            }
            $newAdmin = $this->zeptoRepository->changeStatus($id, $status);
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
            $isExist = $this->zeptoRepository->checkIfExist($id);
            if(!$isExist){
                // admin not found
                $text = APIUserResponse::$invalidZeptoid;
                $mainData= [];
                $hint = ["Ensure to use the method stated in the documentation.",'Pass in valid zepto id.'];
                $linktosolve = "https://";
                $errorCode = APIErrorCode::$internalUserWarning;
                return $this->respondBadRequest($mainData, $text, $hint, $linktosolve, $errorCode);
            }
            $newAdmin = $this->zeptoRepository->deleteZepto($id);
            $text = APIUserResponse::$deleteZepto;
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
