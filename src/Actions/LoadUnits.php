<?php

namespace Fpaipl\Prody\Actions;

use Fpaipl\Prody\Models\Unit;
use Fpaipl\Panel\Services\Syncme;
use Illuminate\Support\Facades\Log;

class LoadUnits
{
    public static function execute($debug = false): int
    {
        if ($debug) {
            Log::info('LoadUnitsAction execute start');
        }

        try {

            $apiRoute = config('monaal.api.sync.taxes');
            $response = (new Syncme(true, config('monaal')))->post($apiRoute);

            if (!isset($response['data']) || $response['status'] == 'error') {
                throw new \Exception(isset($response['message']) ? $response['message'] : 'Unknown error');
            }

            if ($debug) {
                Log::info('LoadUnitsAction response: ', (array)$response);
            }

            $count = self::saveData($response, $debug);
            Log::info('Total new units created: ' . $count);

            return $count;

        } catch (\Exception $e) {
            if ($debug) {
                Log::error('LoadUnitsAction error: ', ['error' => $e->getMessage()]);
            }
            return 0;
        }
    }

    private static function saveData(array $data, bool $debug = false): int
    {
        $count = 0;

        // Determine the structure of the data
        $datalist = is_string($data['data']) ? json_decode($data['data'], true) : $data['data'];

        // Debug log to ensure data structure is as expected.
        if ($debug) {
            Log::info('LoadUnitsAction data structure: ', $datalist);
        }
        
        // Now if array is not empty, loop through the data and save it to the database.
        if (!empty($datalist)) {
            foreach ($datalist as $item) {
                $newBrand = Unit::updateOrCreate(
                    ['monaal_id' => $item['id']],
                    [
                        'name' => $item['name'],
                        'names' => $item['names'],
                        'abbr' => $item['abbr'],
                        'abbrs' => $item['abbrs'],
                    ]
                );
                
                if ($newBrand->wasRecentlyCreated) {
                    $count++;
                }

                if ($debug) {
                    Log::info('LoadUnitsAction newBrand: ', [$newBrand->toArray()]);
                }
            }
        }

        return $count;
    }
}
