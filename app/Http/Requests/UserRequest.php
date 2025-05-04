<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

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
        $rules = [];

        if ($this->isMethod('post')) {
            $rules = [
                'first_name'    =>  'required|string',
                'last_name'     =>  'required|string',
                'email'         =>  'required|email|unique:user,email',
                'password'      =>  'required|string|min:8',
                'phone'         =>  'required|string',
                'dob'           =>  'required|date',
                'gender'        =>  'required|in:m,f,o',
                'address'       =>  'required|string',
                'role_type'     =>  'required|in:super_admin,artist_manager,artist'
            ];
        }

        if ($this->isMethod('patch') || $this->isMethod('put')) {
            $rules = [
                'id'            =>  'required|exists:user,id',
                'first_name'    =>  'required|string',
                'last_name'     =>  'required|string',
                'email'         =>  [
                                        'required',
                                        'email',
                                        Rule::unique('user', 'email')->ignore($this->id),
                                    ],
                'password'      =>  'nullable|string|min:8',
                'phone'         =>  'required|string',
                'dob'           =>  'required|date',
                'gender'        =>  'required|in:m,f,o',
                'address'       =>  'required|string',
                'role_type'     =>  'required|in:super_admin,artist_manager,artist'
            ];
        }

        if ($this->isMethod('delete') || $this->isMethod('get')) {
            $rules = [
                'id'            => 'required|exists:user,id'
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
