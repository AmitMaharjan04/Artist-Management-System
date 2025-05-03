<?php

namespace App\Http\Helpers;

class ApiResponseHelper
{
    public static function getData($data, $status = '1', $message = 'Data Found.')
    {
        $data =  [
            'status'        => $status,
            'statusCode'    => 200,
            'message'       => $message,
            'response'      => $data
        ];
        return response()->json($data, $data['statusCode']);
    }

    public static function created($message = 'Data Created.', $data = [], $status = '1')
    {
        $data =  [
            'status'        => $status,
            'statusCode'    => 201,
            'message'       => $message,
            ...(count($data) ? ['response'      => $data] : []),
        ];
        return response()->json($data, $data['statusCode']);
    }

    public static function success($message, $status = '1')
    {
        $data = [
            'status'        => $status,
            'statusCode'    => 200,
            'message'       => $message
        ];
        return response()->json($data, $data['statusCode']);
    }
    
    public static function loginSuccessful($data, $token, $status = '1')
    {
        $data = [
            'status'        => $status,
            'statusCode'    => 200,
            'message'       => 'Login Successful.',
            'api_token'     => $token,
            'response'      => $data
        ];
        return response()->json($data, $data['statusCode']);
    }

    public static function unauthorized($message, $status = '0')
    {
        $data = [
            'status'        => $status,
            'statusCode'    => 401,
            'message'       => $message
        ];
        return response()->json($data, $data['statusCode']);
    }
    
    public static function error($message, $status = '0')
    {
        $data = [
            "status"        => $status,
            'statusCode'    => 403,
            'message'       => $message
        ];
        return response()->json($data, $data['statusCode']);
    }
}
