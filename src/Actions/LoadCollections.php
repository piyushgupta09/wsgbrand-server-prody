<?php

namespace Fpaipl\Prody\Actions;

use Fpaipl\Panel\Services\Syncme;
use Fpaipl\Prody\Models\Collection;
use Illuminate\Support\Facades\Log;

class LoadCollections
{
    public static function execute($debug = false): int
    {
        if ($debug) {
            Log::info('LoadCollectionsAction execute start');
        }

        try {
            $apiRoute = config('wsg.api.sync.collections') . '/' . config('wsg.brand_id');
            $response = (new Syncme(true, config('wsg')))->post($apiRoute);

            if (!isset($response['data']) || $response['status'] == 'error') {
                throw new \Exception(isset($response['message']) ? $response['message'] : 'Unknown error');
            }

            if ($debug) {
                Log::info('LoadCollectionsAction response: ', (array)$response);
            }

            $count = self::saveData($response, $debug);
            Log::info('Total new collections created: ' . $count);

            return $count;
        } catch (\Exception $e) {
            if ($debug) {
                Log::error('LoadCollectionsAction error: ', ['error' => $e->getMessage()]);
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

                // Debug log to ensure data structure is as expected.
                if ($debug) {
                    Log::info('Processing collection data: ', $item);
                }

                $images = isset($item['images']) ? json_encode($item['images']) : null;

                $newCollection = Collection::updateOrCreate(
                    ['wsg_id' => $item['id']],
                    [
                        'name' => $item['name'],
                        'slug' => $item['slug'],
                        'type' => $item['type'],
                        'active' => $item['active'],
                        'order' => $item['order'],
                        'tags' => $item['tags'],
                        'info' => $item['info'],
                        'images' => $images,
                    ]
                );

                // Increment count if a new collection is created or updated.
                if ($newCollection->wasRecentlyCreated || $newCollection->wasChanged()) {
                    $count++;
                }
            }
        }

        return $count;
    }
}
