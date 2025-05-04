<?php

namespace App\Http\Controllers;

use App\Http\Helpers\ApiResponseHelper;
use App\Http\Repositories\Interfaces\ArtistInterface;
use App\Http\Requests\DataTableRequest;
use App\Http\Requests\ArtistRequest;
use App\Http\Services\ArtistService;
use Illuminate\Http\Request;

class ArtistController extends Controller
{
    public function __construct(protected ArtistInterface $artistRepository, protected ArtistService $artistService) {}

    public function index(DataTableRequest $request)
    {
        $data = $this->artistRepository->lists(
            $request->page,
            $request->size,
            $request->sort_field ?? 'created_at',
            $request->sort_dir ?? 'desc',
            $request->filter ?? [],
        );
        return ApiResponseHelper::getData($data);
    }

    public function store(ArtistRequest $request)
    {
        $this->artistRepository->create($request->validated());
        return ApiResponseHelper::created("Artist created successfully!");
    }

    public function show(ArtistRequest $request)
    {
        $toReturn = $this->artistRepository->find($request->id);
        return ApiResponseHelper::getData($toReturn);
    }

    public function update(ArtistRequest $request)
    {
        $this->artistRepository->update($request->id, $request->validated());
        return ApiResponseHelper::success("Artist updated successfully!");
    }

    public function delete(ArtistRequest $request)
    {
        $this->artistRepository->delete($request->id);
        return ApiResponseHelper::success("Artist deleted successfully!");
    }

    public function import(Request $request)
    {
        $message = $this->artistService->import($request->file('csv_file'));
        if($message == "imported")
            return ApiResponseHelper::success("Artist imported successfully!");

        return ApiResponseHelper::error($message);
    }

    public function allArtists()
    {
        $artists = $this->artistRepository->allArtists();
        $artists = array_map(function ($row) {
            return [
                'id'    => $row->id,
                'name'  => $row->name,
            ];
        }, array: $artists);
        return ApiResponseHelper::getData($artists);
    }
}
