<?php

namespace App\Http\Controllers;

use App\Http\Helpers\ApiResponseHelper;
use App\Http\Repositories\Interfaces\SongInterface;
use App\Http\Requests\DataTableRequest;
use App\Http\Requests\SongRequest;
use App\Http\Services\SongService;
use Illuminate\Http\Request;

class SongController extends Controller
{
    public function __construct(protected SongInterface $songRepository, protected SongService $songService) {}

    public function index(Request $request)
    {
        $filter = $request->filter ?? [];
        if($request->artist_id)
            $filter['artist_id'] = $request->artist_id;

        $data = $this->songRepository->lists(
            $request->page,
            $request->size,
            $request->sort_field ?? 'created_at',
            $request->sort_dir ?? 'desc',
            $filter,
        );
        return ApiResponseHelper::getData($data);
    }

    public function store(SongRequest $request)
    {
        $this->songRepository->create($request->validated());
        return ApiResponseHelper::created("Song created successfully!");
    }

    public function show(SongRequest $request)
    {
        $toReturn = $this->songRepository->find($request->validated());
        return ApiResponseHelper::getData($toReturn);
    }

    public function update(SongRequest $request)
    {
        $this->songRepository->update($request->id, $request->validated());
        return ApiResponseHelper::success("Song updated successfully!");
    }

    public function delete(SongRequest $request)
    {
        $this->songRepository->delete($request->updated_at);
        return ApiResponseHelper::success("Song deleted successfully!");
    }

}
