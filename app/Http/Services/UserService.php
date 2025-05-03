<?php

namespace App\Http\Services;

use App\Http\Repositories\Interfaces\UserInterface;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function __construct(private UserInterface $userRepository) {}

    public function update($id, $data)
    {
        $user = $this->userRepository->find($id);
        if($data['password'] && $data['password'] != "") {
            $data['password'] = Hash::make($data['password']);
        } else {
            $data['password'] = $user->password;
        }
        return $this->userRepository->update($id, $data);
    }
    
    public function register($params)
    {
        $params['password'] = Hash::make($params['password']);
        return $this->userRepository->create($params);
    }
}
