<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

class ArtistRequest extends FormRequest
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

        if ($this->isMethod('post') || $this->isMethod('patch')) {
            $rules = [
                'name'                  =>  'required|string',
                'dob'                   =>  'required|date',
                'gender'                =>  'required|in:m,f,o',
                'address'               =>  'required|string',
                'first_release_year'    =>  [
                                                'required',
                                                'integer',
                                                'digits:4',
                                                'min:1900',
                                                'max:' . date('Y'),
                                            ],
                'no_of_albums_released' =>  'required|integer',
            ];
        }

        if ($this->isMethod('delete') || $this->isMethod('get')) {
            $rules = [
                'id'            => 'required|exists:artist,id'
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
