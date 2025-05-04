<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

class SongRequest extends FormRequest
{
    /**
     * Determine if the song is authorized to make this request.
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
                'artist_id'     =>  'required|exists:artist,id',
                'title'         =>  'required|string',
                'album_name'    =>  'required',
                'genre'         =>  'required|in:rnb,country,classic,rock,jazz',
            ];
        }

        if ($this->isMethod('patch') || $this->isMethod('put')) {
            $rules = [
                'id'            =>  'required',
                'artist_id'     =>  'required|exists:artist,id',
                'title'         =>  'required|string',
                'album_name'    =>  'required',
                'genre'         =>  'required|in:rnb,country,classic,rock,jazz',
                // 'updated_at'    =>  'required',
            ];
        }

        if ($this->isMethod('delete') || $this->isMethod('get')) {
            $rules = [
                'artist_name'            =>   'required|exists:artist,name',
                'updated_at'           =>   'required',
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
