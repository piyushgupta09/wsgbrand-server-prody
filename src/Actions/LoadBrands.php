<?php

namespace Fpaipl\Prody\Actions;

use Fpaipl\Prody\Models\Brand;
use Fpaipl\Panel\Services\Syncme;
use Illuminate\Support\Facades\Log;

class LoadBrands
{
    public static function execute($debug = false): int
    {
        if ($debug) {
            Log::info('LoadBrandAction execute start');
        }

        try {

            $apiRoute = config('wsg.api.sync.brands') . '/' . config('wsg.brand_id');
            $response = (new Syncme(true, config('wsg')))->post($apiRoute);

            if (!isset($response['data']) || $response['status'] == 'error') {
                throw new \Exception(isset($response['message']) ? $response['message'] : 'Unknown error');
            }

            if ($debug) {
                Log::info('LoadBrandAction response: ', (array)$response);
            }

            $count = self::saveData($response, $debug);
            Log::info('Total new brands created: ' . $count);

            return $count;

        } catch (\Exception $e) {
            if ($debug) {
                Log::error('LoadBrandAction error: ', ['error' => $e->getMessage()]);
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
            Log::info('LoadBrandAction data structure: ', $datalist);
        }
        
        // Now if array is not empty, loop through the data and save it to the database.
        if (!empty($datalist)) {
            foreach ($datalist as $item) {
                $newBrand = Brand::updateOrCreate(
                    ['wsg_id' => $item['id']],
                    [
                        'name' => $item['name'],
                        'slug' => $item['slug'],
                        'tagline' => $item['tagline'],
                        'description' => $item['description'],
                        'website' => $item['website'],
                        'email' => $item['email'],
                        'contact_number' => $item['contact_number'],
                        'contact_person' => $item['contact_person'],
                        'active' => $item['active'],
                        'tags' => $item['tags'],
                        'images' => json_encode($item['images']),
                    ]
                );
                
                if ($newBrand->wasRecentlyCreated) {
                    $count++;
                }

                if ($debug) {
                    Log::info('LoadBrandAction newBrand: ', [$newBrand->toArray()]);
                }
            }
        }

        return $count;
    }
}
