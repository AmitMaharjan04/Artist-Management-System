<?php

namespace Database\Seeders;

use App\Http\Repositories\Interfaces\UserInterface;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function __construct(private UserInterface $userRepository) {}
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            'first_name'    =>  "Foo",
            'last_name'     =>  "Bar",
            'email'         =>  'admin@gmail.com',
            'password'      =>  Hash::make('admin@123'),
            'phone'         =>  '1234567890',
            'dob'           =>  '2000-01-01 00:11:22',
            'gender'        =>  'm',
            'address'       =>  '123, ABC',
            'role_type'     =>  'super_admin'
        ];
        $this->userRepository->create($data);
    }
}
