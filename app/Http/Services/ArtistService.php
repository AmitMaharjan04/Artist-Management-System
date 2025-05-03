<?php

namespace App\Http\Services;

use App\Http\Repositories\Interfaces\ArtistInterface;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use League\Csv\Reader;

class ArtistService
{
    public function __construct(private ArtistInterface $artistRepository) {}

    public function import($file)
    {
        try {
            $csv = Reader::createFromPath($file->getPathname(), 'r');
            $csv->setHeaderOffset(0);

            $headers = $csv->getHeader();
            $requiredHeaders = ['Name', 'Date of Birth', 'Gender', 'Address', 'First Released Year', 'Albums Released'];
            $missingHeaders = array_diff($requiredHeaders, $headers);
            if (!empty($missingHeaders)) {
                return "Header format is incorrect!";
            }

            // Read all records
            $records = iterator_to_array($csv->getRecords());
            if (empty($records)) {
                return "CSV file is empty!";
            }

            $errors = [];
            $lineNumber = 1; // Start at line 1 (after headers)

            foreach ($records as $index => $record) {
                $lineNumber++;
                $transformed = [
                    'name'                  => $record['Name'] ?? null,
                    'dob'                   => $record['Date of Birth'] ?? null,
                    'gender'                => $record['Gender'] ?? null,
                    'address'                => $record['Address'] ?? null,
                    'first_release_year'    => $record['First Released Year'] ?? null,
                    'no_of_albums_released' => $record['Albums Released'] ?? null,
                ];
                
                $recordValidator = Validator::make($transformed, [
                    'name'                  => 'required|string|max:255',
                    'dob'                   => 'required|date',
                    'gender'                => 'required|in:m,f,o',
                    'address'                => 'required',
                    'first_release_year'    => 'required|integer|min:1900|max:' . date('Y'),
                    'no_of_albums_released' => 'required|integer|min:0',
                ]);

                if ($recordValidator->fails()) {
                    foreach ($recordValidator->errors()->toArray() as $field => $fieldErrors) {
                        foreach ($fieldErrors as $error) {
                            $errors[] = "Line {$lineNumber}: {$error}";
                        }
                    }
                }
            }

            if (!empty($errors)) {
                Log::error(print_r($errors, true));
                return $errors;
            }

            DB::beginTransaction();
            try {
                foreach ($records as $record) {
                    $transformed = [
                        'name'                  => $record['Name'] ?? null,
                        'dob'                   => $record['Date of Birth'] ?? null,
                        'gender'                => $record['Gender'] ?? null,
                        'address'               => $record['Address'] ?? null,
                        'first_release_year'    => $record['First Released Year'] ?? null,
                        'no_of_albums_released' => $record['Albums Released'] ?? null,
                    ];
                    $data = [
                        'name'                  => $transformed['name'],
                        'dob'                   => $transformed['dob'],
                        'gender'                => $transformed['gender'],
                        'address'               => $transformed['address'],
                        'first_release_year'    => $transformed['first_release_year'],
                        'no_of_albums_released' => $transformed['no_of_albums_released'],
                    ];
                    $this->artistRepository->create($data);
                }
                DB::commit();

                return "imported";
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error("An error occured during importing!" . print_r($e->getMessage(), true));
                return "An error occured during importing!";
            }
        } catch (\Exception $e) {
            Log::error("Failed to process CSV file!" . print_r($e->getMessage(), true));
            return "Failed to process CSV file!";
        }
    }
}
