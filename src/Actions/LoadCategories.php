<?php

namespace Fpaipl\Prody\Actions;

use Fpaipl\Panel\Services\Syncme;
use Fpaipl\Prody\Models\Category;
use Illuminate\Support\Facades\Log;

class LoadCategories
{
    public static function execute($debug = false): int
    {
        if ($debug) {
            Log::info('LoadCategoriesAction execute start');
        }

        try {
            $apiRoute = config('wsg.api.sync.categories') . '/' . config('wsg.brand_id');
            $response = (new Syncme(true, config('wsg')))->post($apiRoute);

            if (!isset($response['data']) || $response['status'] == 'error') {
                throw new \Exception(isset($response['message']) ? $response['message'] : 'Unknown error');
            }

            if ($debug) {
                Log::info('LoadCategoriesAction response: ', (array)$response);
            }

            $count = self::saveData($response, $debug);
            Log::info('Total new categories created: ' . $count);

            return $count;
        } catch (\Exception $e) {
            if ($debug) {
                Log::error('LoadCategoriesAction error: ', ['error' => $e->getMessage()]);
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
            Log::info('LoadCategoriesAction data structure: ', $datalist);
        }
        
        // Now if array is not empty, loop through the data and save it to the database.
        if (!empty($datalist)) {
            foreach ($datalist as $item) {

                $newCategory = Category::updateOrCreate(
                    ['wsg_id' => $item['id']],
                    [
                        'wsg_parent_id' => $item['wsg_parent_id'] ?? null,
                        'parent_id' => $item['parent_id'] ?? null,
                        'name' => $item['name'],
                        'slug' => $item['slug'],
                        'info' => $item['info'],
                        'tags' => $item['tags'],
                        'order' => $item['order'],
                        'display' => $item['display'],
                        'active' => $item['active'],
                        'images' => json_encode($item['images']),
                    ]
                );
                if ($newCategory->wasRecentlyCreated) {
                    $count++;
                }

                if ($debug) {
                    Log::info('LoadCategoriesAction newCategory: ', ['name' => $newCategory->name]);
                }
            }
        }

        // Return the count of new categories created.
        return $count;
    }
}
