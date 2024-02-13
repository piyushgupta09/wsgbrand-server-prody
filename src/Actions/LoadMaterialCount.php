<?php

namespace Fpaipl\Prody\Actions;

use Fpaipl\Panel\Services\Syncme;
use Illuminate\Support\Facades\Log;

class LoadMaterialCount
{
    /**
     * Execute the action.
     *
     * @param bool $debug
     * @param string $supplierName
     * @return int
     */
    public static function execute($supplierName, $debug = false): int
    {
        if ($debug) {
            Log::info('LoadMaterialCount execute start: ', ['supplierName' => $supplierName]);
        }

        try {
            $response = null;

            switch ($supplierName) {
                case config('monaal.url'):
                    $response = (new Syncme($debug))->post(config('monaal.api.sync.products_count'));
                    break;

                default:
                    throw new \Exception("Unsupported supplier: {$supplierName}");
            }

            if ($debug) {
                Log::info('LoadMaterialCountAction response: ', (array)$response);
            }

            if (!isset($response['data']) || $response['status'] == 'error') {
                throw new \Exception(isset($response['message']) ? $response['message'] : 'Unknown error');
            }

            return $response['data']['count'] ?? 0;

        } catch (\Exception $e) {
            if ($debug) {
                Log::error('LoadMaterialCountAction error: ', ['error' => $e->getMessage()]);
            }
            return 0;
        }
    }
}
