<?php

namespace App\Http\Controllers;

use App\Http\Helpers\ApiResponseHelper;
use App\Http\Services\AuthService;
use App\Http\Requests\AuthRequest;

class AuthController extends Controller
{
    public function __construct(private AuthService $authService) {}

    public function login(AuthRequest $request) 
    {
        $data = $this->authService->login($request->validated());
        if (!$data) 
            return ApiResponseHelper::error('Either username or password is incorrect!');

        return ApiResponseHelper::getData($data);
    }
    
    public function register(AuthRequest $request)
    {
        $data = $this->authService->register($request->validated());
        if(!$data)
            return ApiResponseHelper::error("Admin User registration failed.");

        return ApiResponseHelper::created("Admin User registered successfully.");
    }
}
