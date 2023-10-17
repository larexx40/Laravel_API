<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class UserBankAccountRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            "bankid" => "required|max:50",
            "user_id" => "required|max:50",
            "bank_name" => "required|max:50",
            "account_no" => "required|max:15",
            "account_name" => "required|max:100",
            "sys_bank_id" => "required|max:50"
        ];
        if($this->getMethod() == "PUT" || $this->getMethod() == "PATCH" || $this->getMethod() == "DELETE"){
            $rules["is_default"] = "required";
            $rules["status"] = "required";
            $rules["bankid"] = "required|max:20";
        }
        return $rules;
    }

    public function failedValidation(Validator $validator)
    {
        
        $method = request()->method();
        $endpoint = request()->fullUrl();
        $errordata = [
            "text" => "Data sent by the user is not valid",
            "hint" => $validator->errors()->all(),
        ];
        throw new HttpResponseException(response()->json([
            'status'   => false,
            'text'   => 'Validation errors',
            // 'errorData'      => $validator->errors()->all(),
            "time" => now()->format('d-m-y H:i:sA'),
            "method" => $method,
            "endpoint" => $endpoint,
            "error" => $errordata
        ], 422));
    }
}
