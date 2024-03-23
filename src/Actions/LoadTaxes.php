<?php

namespace Fpaipl\Prody\Actions;

use Fpaipl\Prody\Models\Tax;
use Fpaipl\Panel\Services\Syncme;
use Illuminate\Support\Facades\Log;

class LoadTaxes
{
    public static function execute($debug = false): int
    {
        if ($debug) {
            Log::info('LoadTaxesAction execute start');
        }

        try {

            $apiRoute = config('wsg.api.sync.taxes') . '/' . config('wsg.brand_id');
            $response = (new Syncme(true, config('wsg')))->post($apiRoute);

            if (!isset($response['data']) || $response['status'] == 'error') {
                throw new \Exception(isset($response['message']) ? $response['message'] : 'Unknown error');
            }

            if ($debug) {
                Log::info('LoadTaxesAction response: ', (array)$response);
            }

            $count = self::saveData($response, $debug);
            Log::info('Total new taxes created: ' . $count);

            return $count;

        } catch (\Exception $e) {
            if ($debug) {
                Log::error('LoadTaxesAction error: ', ['error' => $e->getMessage()]);
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
            Log::info('LoadTaxesAction data structure: ', $datalist);
        }
        
        // Now if array is not empty, loop through the data and save it to the database.
        if (!empty($datalist)) {
            foreach ($datalist as $item) {
                $newBrand = Tax::updateOrCreate(
                    ['wsg_id' => $item['id']],
                    [
                        'name' => $item['name'],
                        'tags' => $item['tags'],
                        'hsncode' => $item['hsncode'],
                        'description' => $item['description'],
                        'gstrate' => $item['gstrate'],
                        'active' => $item['active'],
                    ]
                );
                
                if ($newBrand->wasRecentlyCreated) {
                    $count++;
                }

                if ($debug) {
                    Log::info('LoadTaxesAction newBrand: ', [$newBrand->toArray()]);
                }
            }
        }

        return $count;
    }
}
