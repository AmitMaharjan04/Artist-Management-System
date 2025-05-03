<?php

namespace App\Http\Controllers;

use App\Http\Helpers\ApiResponseHelper;
use App\Http\Repositories\Interfaces\UserInterface;
use App\Http\Requests\DataTableRequest;
use App\Http\Requests\UserRequest;
use App\Http\Services\UserService;

class UserController extends Controller
{
    public function __construct(protected UserInterface $userRepository, protected UserService $userService) {}

    public function index(DataTableRequest $request)
    {
        $data = $this->userRepository->lists(
            $request->page,
            $request->size,
            $request->sort_field ?? 'created_at',
            $request->sort_dir ?? 'desc',
            $request->filter ?? [],
        );
        return ApiResponseHelper::getData($data);
    }

    public function store(UserRequest $request)
    {
        $this->userRepository->create($request->validated());
        return ApiResponseHelper::created("Admin User created successfully!");
    }

    public function show(UserRequest $request)
    {
        $toReturn = $this->userRepository->find($request->id);
        unset($toReturn->password);
        return ApiResponseHelper::getData($toReturn);
    }

    public function update(UserRequest $request)
    {
        $this->userService->update($request->id, $request->validated());
        return ApiResponseHelper::success("Admin User updated successfully!");
    }

    public function delete(UserRequest $request)
    {
        $this->userRepository->delete($request->id);
        return ApiResponseHelper::success("Admin User deleted successfully!");
    }

}
