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
            "bankid" => "required|max:50|unique:user_bank_accounts,bankid",
            "user_id" => "required|max:50",
            "bank_name" => "max:50",
            "account_no" => "max:15",
            "account_name" => "max:100",
            "sys_bank_id" => "max:50"
        ];
        if($this->getMethod() == "PUT" || $this->getMethod() == "PATCH" || $this->getMethod() == "DELETE"){
            $rules["bankid"] = "required|max:20";
        }
        return $rules;
    }

    public function failedValidation(Validator $validator)
    {
        
        $method = request()->method();
        $endpoint = request()->fullUrl();
        $errordata = [
            "text" => "Validation errors",
            "hint" => $validator->errors()->all(),
        ];
        throw new HttpResponseException(response()->json([
            'status'   => false,
            'text'   => 'Data sent by the user is not valid',
            // 'errorData'      => $validator->errors()->all(),
            "time" => now()->format('d-m-y H:i:sA'),
            "method" => $method,
            "endpoint" => $endpoint,
            "error" => $errordata
        ], 422));
    }
}
