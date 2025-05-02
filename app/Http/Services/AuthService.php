<?php

namespace App\Http\Services;

use App\Http\Repositories\Interfaces\UserInterface;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Hash;

use function PHPSTORM_META\type;

class AuthService
{
    public function __construct(private UserInterface $userRepository) {}

    public function login($data)
    {
        $user = $this->userRepository->findByEmail($data['email']);
        
        // $user = $this->userRepository->lists(1,4,"first_name","asc",[]);
        if (!$user || !Hash::check($data['password'], $user->password)) {
            return false;
        }
        $response = [];
        $payload = [
            'sub' => $user->id,
            'email' => $user->email,
            'iat' => time(),
            'exp' => time() + (60 * 60),
        ];

        $token = JWT::encode($payload, config('app.jwt-key'), 'HS256');
        
        $superAdmin = ($user->role_type === 'superadmin') ? true : false;

        $response = [
            'superAdmin'    => $superAdmin,
            'token'         => $token
        ];
        return $response;
    }
    
    public function register($params)
    {
        $params['password'] = Hash::make($params['password']);
        return $this->userRepository->create($params);
    }
}
