<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class UserRequest extends FormRequest
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
        return [
            //
            'fname' => 'required|string|max:150',
            'lname' => 'required|string|max:150',
            'username' => 'string|max:150',
            'email' => 'required|string|email|unique:users,email',
            'refby' => 'string|max:100',
            'userid' => 'string|max:100|unique:users,userid',
            'userpubkey' => 'string|max:200|unique:users,userpubkey',
            'password' => 'required|string|min:6|regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{6,}$/',
            'phoneno' => ['required', 'string', 'regex:/^[0-9]{11}$/'],
            'date_of_birth' => ['required', 'date', 'before:today'],

        ];
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
