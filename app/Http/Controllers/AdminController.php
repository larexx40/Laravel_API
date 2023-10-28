<?php

namespace App\Http\Controllers;

use App\Config\APIErrorCode;
use App\Config\APIUserResponse;
use App\Models\Admin;
use App\Http\Controllers\Controller;
use App\Http\Resources\AdminResource;
use App\Interfaces\AdminInterface;
use App\Utilities\UtilityFunctions;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AdminController extends BaseController
{

    private AdminInterface $adminRepository;
    public function __construct(AdminInterface $adminRepository){
        $this->adminRepository = $adminRepository;
    }
    /**
     * Display a listing of the resource.
     */
    public function getAllAdmin()
    {
        //
        try{
            $admins =  $this->adminRepository->getAllAdmin();
            $text = (count($admins) > 0)? APIUserResponse::$getRequestNoRecords : APIUserResponse::$getRequestNoRecords;
            $admins = AdminResource::collection($admins);
            return $this->respondOK($admins, $text);
        }catch(QueryException $e){
            return $this->handleQueryException($e);
        }catch(\Exception $e){
            return $this->handleException($e);
        }

    }

    /**
     * Show the form for creating a new resource.
     */
    public function addAdmin(Request $request)
    {
        //
        $input = $request->only([
            'name', 'username', 'email', 'adminlevel', 'password'
        ]);
        //validate input
        $validator = Validator::make($input, [
                'name' => 'required',   
                'adminlevel' => 'required',
                'email' => 'required|string|email|unique:admins,email',
                'username' => 'required|string|unique:admins,username',
                'password' => 'required|string|min:6|regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{6,}$/',
            ],
            $messages =[
                'email.unique' => 'Email already exist.',
                'password.regex' => 'Password must contain at least one uppercase letter, one lowercase letter, one digit, and one special character (#?!@$%^&*-).',
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
        $input['adminid'] = UtilityFunctions::generateUniqueShortKey("admins", "adminid");
        $input['adminpubkey'] = UtilityFunctions::generateUniquePubKey("admins", "adminpubkey");
        $input['password'] = bcrypt($input['password']);
        $input['status'] = 1;

        try {
            $newAdmin = $this->adminRepository->addAdmin($input);
            $text = APIUserResponse::$adminAdded;
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
    public function updateAdminDetails(Request $request)
    {
        //
        $input = $request->only([
            'adminid', 'name', 'username', 'phoneno'
        ]);
        //validate input
        $validator = Validator::make($input, [
                'adminid'=>'required|string|exists:admins,adminid',
                'name' => 'required',   
                'username' => 'required|string|unique:admins,username',
            ],
            $messages =[
                'adminid.exists' => 'Admin with id does not exist.',
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
            $newAdmin = $this->adminRepository->updateAdmin($input);
            $text = APIUserResponse::$adminDetailsUpdate;
            $mainData = [];
            return $this->respondOK($mainData, $text);

        }catch(QueryException $e){
            return $this->handleQueryException($e);
        }catch(\Exception $e){
            return $this->handleException($e);
        }


    }

    public function updateNewAdminDetails(Request $request)
    {
        //
        $input = $request->only([
            'adminid', 'name', 'username', 'phoneno', 'password'
        ]);
        //validate input
        $validator = Validator::make($input, [
                'adminid'=>'required|string|exists:admins,adminid',
                'name' => 'required',   
                'username' => 'required|string|unique:admins,username',
                'phoneno' => [ 'required'.'string', 'regex:/^[0-9]{11}$/'],
                'password' => 'required|string|min:6|regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{6,}$/',
            ],
            $messages =[
                'adminid.exists' => 'Admin with id does not exist.',
                'password.regex' => 'Password must contain at least one uppercase letter, one lowercase letter, one digit, and one special character (#?!@$%^&*-).',
                'phoneno.regex' => 'The phone number must be 11 digits in length and contain only numbers.',
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
            $input['password'] = bcrypt($input['password']);
            $input['profile_updated'] = 1;
            $input['password_updated'] = 1;
            $newAdmin = $this->adminRepository->updateAdmin($input);
            $text = APIUserResponse::$adminDetailsUpdate;
            $mainData = [];
            return $this->respondOK($mainData, $text);

        }catch(QueryException $e){
            return $this->handleQueryException($e);
        }catch(\Exception $e){
            return $this->handleException($e);
        }


    }

    /**
     * Display the specified resource.
     */
    public function changeAdminStatus(Request $request)
    {
        //
        $input = $request->only([
                'adminid', 'status'
        ]);
        //validate input
        $validator = Validator::make($input, [
                'adminid'=>'required|string|exists:admins,adminid',
                'status' => 'required|in:1,0',
            ],
            $messages =[
                'adminid.exists' => 'Admin with id does not exist.',
                'status.in' => 'Status must be 1 or 0.',
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
            $adminid = $input['adminid'];
            $status = $input['status'];
            $newAdmin = $this->adminRepository->changeAdminStatus($adminid, $status);
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
    public function deleteAdmin(String $adminid)
    {
        // find admin by adminid
        try {
            $admin = $this->adminRepository->getAdminData("adminid", $adminid, "adminid, adminlevel, name");
            if(empty($admin)){
                // admin not found
                $text = APIUserResponse::$invalidAdminId;
                $mainData= [];
                $hint = ["Ensure to use the method stated in the documentation.",'Pass in valid adminid.'];
                $linktosolve = "https://";
                $errorCode = APIErrorCode::$internalUserWarning;
                return $this->respondBadRequest($mainData, $text, $hint, $linktosolve, $errorCode);
            }

            //check if its not a super admin
            if($admin->adminlevel == 1){
                $text = APIUserResponse::$deleteSuperAdmin;
                $mainData= [];
                $hint = ["Ensure to use the method stated in the documentation.",'Amin passed is a super admin.'];
                $linktosolve = "https://";
                $errorCode = APIErrorCode::$internalUserWarning;
                return $this->respondBadRequest($mainData, $text, $hint, $linktosolve, $errorCode);
            }
            //delete admin
            $delete = $this->adminRepository->deleteAdmin($adminid);
            $text = APIUserResponse::$deleteAdmin ;
            $mainData= [];
            return $this->respondOK($mainData, $text);

        }catch(QueryException $e){
            return $this->handleQueryException($e);
        }catch(\Exception $e){
            return $this->handleException($e);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function changeAdminPassword(Request $request)
    {
        //
        $input = $request->only([
            'adminid', 'password'
        ]);
        //validate input
        $validator = Validator::make($input, [
                'adminid'=>'required|string|exists:admins,adminid',
                'password' => 'required|string|min:6|regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{6,}$/',
            ],
            $messages =[
                'adminid.exists' => 'Admin with id does not exist.',
                'password.regex' => 'Password must contain at least one uppercase letter, one lowercase letter, one digit, and one special character (#?!@$%^&*-).',
                'password.min' => 'Password must be at least 6 characters.',
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
            $input['password'] = bcrypt($input['password']);
            $newAdmin = $this->adminRepository->updateAdmin($input);
            $text = APIUserResponse::$adminDetailsUpdate;
            $mainData = [];
            return $this->respondOK($mainData, $text);

        }catch(QueryException $e){
            return $this->handleQueryException($e);
        }catch(\Exception $e){
            return $this->handleException($e);
        }


    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Admin $admin)
    {
        //
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
