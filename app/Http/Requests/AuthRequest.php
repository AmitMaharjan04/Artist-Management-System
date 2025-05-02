<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Symfony\Component\HttpFoundation\Response;

class AuthRequest extends FormRequest
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
        if($this->route()->uri() === "api/login") {
            $rules = [
                'email'     =>  'required|email|exists:user,email',
                'password'  =>  'required|string',
            ];
        } else {
            $rules = [
                'first_name'    =>  'required|string',
                'last_name'     =>  'required|string',
                'email'         =>  'required|email|unique:user,email',
                'password'      =>  'required|string',
                'phone'         =>  'required|string',
                'dob'           =>  'required|date',
                'gender'        =>  'required|in:m,f,o',
                'address'       =>  'required|string',
                'role_type'     =>  'required|in:superadmin,artist_manager,artist'
            ];
        }
        return $rules;
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'status'    => '0',
            'statusCode' => Response::HTTP_BAD_REQUEST,
            'message'   => $validator->errors()
        ], 400));
    }
}
