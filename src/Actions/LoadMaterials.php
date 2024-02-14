<?php

namespace Fpaipl\Prody\Actions;

use Fpaipl\Panel\Services\Syncme;
use Fpaipl\Prody\Models\Material;
use Illuminate\Support\Facades\Log;
use Fpaipl\Prody\Models\MaterialRange;
use Fpaipl\Prody\Models\MaterialOption;

class LoadMaterials
{
    /**
     * Execute the action.
     *
     * @param bool $debug
     * @param string $supplierName
     * @param int $supplierId
     * @return int
     */
    public static function execute($supplierName, $supplierId, $debug = false): int
    {
        if ($debug) {
            Log::info('LoadMaterialAction execute start: ', ['supplierName' => $supplierName, 'supplierId' => $supplierId]);
        }

        try {
            $response = null;

            switch ($supplierName) {
                case config('monaal.url'):
                    $response = (new Syncme($debug))->post(config('monaal.api.sync.products'));
                    break;

                default:
                    throw new \Exception("Unsupported supplier: {$supplierName}");
            }

            if ($debug) {
                Log::info('LoadMaterialAction response: ', (array)$response);
            }

            if (!isset($response['data']) || $response['status'] == 'error') {
                throw new \Exception(isset($response['message']) ? $response['message'] : 'Unknown error');
            }

            return self::createMaterials($response['data'], $supplierId, $debug);

        } catch (\Exception $e) {
            if ($debug) {
                Log::error('LoadMaterialAction error: ', ['error' => $e->getMessage()]);
            }
            return 0;
        }
    }

    /**
     * Create or update materials and related data
     *
     * @param array $responseData
     * @param int $supplierId
     * @return int
     */
    private static function createMaterials($responseData, $supplierId, $debug): int
    {
        $newMaterialsCount = 0;

        foreach ($responseData as $data) {

            if (!isset($data['sid']) 
            || !isset($data['category_name']) 
            || !isset($data['category_type']) 
            || !isset($data['unit']) || !isset($data['name']) 
            || !isset($data['slug']) || !isset($data['price']) 
            || !isset($data['options']) || !isset($data['ranges'])) {
                if ($debug) {
                    Log::error('LoadMaterialAction error: ', ['error' => 'Invalid material data']);
                }
                continue;
            }

            $newMaterial = Material::updateOrCreate(
                [
                    // Monaal Creation
                    'sid' => 'MC-' . $data['sid'],
                    'supplier_id' => $supplierId,
                ],
                [
                    'category_name' => $data['category_name'],
                    'category_type' => $data['category_type'],
                    'unit_name' => $data['unit']['name'],
                    'unit_abbr' => $data['unit']['abbr'],
                    'name' => $data['name'],
                    'slug' => $data['slug'],
                    'price' => $data['price'],
                    'details' => $data['details'],
                    // 'stock' => $data['stock'],
                    // 'stockItems' => json_encode($data['stockItems']),
                ],
            );

            if ($newMaterial->wasRecentlyCreated) {
                $newMaterialsCount++;
            }

            foreach ($data['options'] as $option) {

                if (!isset($option['name']) || !isset($option['slug']) || !isset($option['code'])) {
                    if ($debug) {
                        Log::error('LoadMaterialAction error: ', ['error' => 'Invalid option data']);
                    }
                    continue;
                }

                MaterialOption::updateOrCreate(
                    [
                        'material_id' => $newMaterial->id,
                        'name' => $option['name']
                    ],
                    [
                        'slug' => $option['slug'],
                        'code' => $option['code'],
                        'image' => $option['image'],
                        'images' => json_encode($option['images']),
                    ]
                );
            }

            foreach ($data['ranges'] as $range) {

                if (!isset($range['width']) || !isset($range['length'])) {
                    if ($debug) {
                        Log::error('LoadMaterialAction error: ', ['error' => 'Invalid range data']);
                    }
                    continue;
                }

                MaterialRange::updateOrCreate(
                    [
                        'material_id' => $newMaterial->id,
                        'width' => $range['width']
                    ],
                    [
                        'length' => $range['length'],
                        'rate' => $range['rate'],
                        'source' => $range['source'],
                        'quality' => $range['quality'],
                        'other' => $range['other'],
                    ]
                );
            }
        }

        return $newMaterialsCount;
    }
}
