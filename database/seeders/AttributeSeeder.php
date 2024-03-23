<?php

namespace Fpaipl\Prody\Database\Seeders;

use Illuminate\Database\Seeder;
use Fpaipl\Prody\Models\Product;
use Fpaipl\Prody\Models\Attrikey;
use Fpaipl\Prody\Models\Attribute;
use Fpaipl\Prody\Models\Measurekey;
use Fpaipl\Prody\Models\ProductAttribute;
use Fpaipl\Prody\Models\ProductMeasurement;

class AttributeSeeder extends Seeder
{
    public function run()
    {
        // Attribute Names
        // Material Style
        // Material Type
        // Stretch Type
        // Stretch Style
        // Neck Type
        // Neck Style
        // Sleeve Type
        // Sleeve Style
        // Wear Type
        // Wear Style
        // Fitting Type
        // Fitting Style
        // Bottom Type
        // Bottom Style
        // Waist Type
        // Waist Style
        // Quantity
        // Chest Pad

        // Attribute Values
        // Printed
        // Polyester
        // Stretchable
        // Semi Stretchable
        // Round Neck
        // Regular 
        // Long Sleeve
        // Plain Sleeve
        // Summer Wear
        // Party Wear
        // Loose Fit
        // Free 
        // Not Applicable
        // Not Applicable
        // Loose Fit
        // Simple
        // 1 Pieces
        // Not Supported

        $attributes = array(
            [
                'name' => 'Material Style',
                'value' => 'Printed',
            ],
            [
                'name' => 'Material Type',
                'value' => 'Polyester',
            ],
            [
                'name' => 'Stretch Type',
                'value' => 'Stretchable',
            ],
            [
                'name' => 'Stretch Style',
                'value' => 'Semi Stretchable',
            ],
            [
                'name' => 'Neck Type',
                'value' => 'Round Neck',
            ],
            [
                'name' => 'Neck Style',
                'value' => 'Regular',
            ],
            [
                'name' => 'Sleeve Type',
                'value' => 'Long Sleeve',
            ],
            [
                'name' => 'Sleeve Style',
                'value' => 'Plain Sleeve',
            ],
            [
                'name' => 'Wear Type',
                'value' => 'Summer Wear',
            ],
            [
                'name' => 'Wear Style',
                'value' => 'Party Wear',
            ],
            [
                'name' => 'Fitting Type',
                'value' => 'Loose Fit',
            ],
            [
                'name' => 'Fitting Style',
                'value' => 'Free',
            ],
            [
                'name' => 'Bottom Type',
                'value' => 'Not Applicable',
            ],
            [
                'name' => 'Bottom Style',
                'value' => 'Not Applicable',
            ],
            [
                'name' => 'Waist Type',
                'value' => 'Loose Fit',
            ],
            [
                'name' => 'Waist Style',
                'value' => 'Simple',
            ],
            [
                'name' => 'Quantity',
                'value' => '1 Pieces',
            ],
            [
                'name' => 'Chest Pad',
                'value' => 'Not Supported',
            ],
        );
        
        foreach ($attributes as $attribute) {
            Attrikey::create([
                'name' => $attribute['name'],
                'detail' => $attribute['value'],
            ]);
        }

        $attrikeys = Attrikey::all();
        foreach ($attrikeys as $attrikey) {
            $attrikey->attrivals()->create([
                'value' => $attrikey['detail'],
                'detail' => $attrikey['detail'],
            ]);
        }

        $measurekeys = array(
            [
                'name' => 'Hips',
                'unit' => 'in',
                'detail' => 'It is the measurement of the hips.',
                'value' => '32, 34, 36, 38, 40, 42, 44, 46, 48, 50',
            ],
            [
                'name' => 'Bust',
                'unit' => 'in',
                'detail' => 'It is the measurement of the bust.',
                'value' => '32, 34, 36, 38, 40, 42, 44, 46, 48, 50',
            ],
            [
                'name' => 'Waist',
                'unit' => 'in',
                'detail' => 'It is the measurement of the waist.',
                'value' => '32, 34, 36, 38, 40, 42, 44, 46, 48, 50',
            ],
            [
                'name' => 'Length',
                'unit' => 'in',
                'detail' => 'It is the measurement of the length.',
                'value' => '32, 34, 36, 38, 40, 42, 44, 46, 48, 50',
            ],
            [
                'name' => 'Shoulder',
                'unit' => 'in',
                'detail' => 'It is the measurement of the shoulder.',
                'value' => '32, 34, 36, 38, 40, 42, 44, 46, 48, 50',
            ],
            [
                'name' => 'Sleeve',
                'unit' => 'in',
                'detail' => 'It is the measurement of the sleeve.',
                'value' => '32, 34, 36, 38, 40, 42, 44, 46, 48, 50',
            ],
            [
                'name' => 'Collar',
                'unit' => 'in',
                'detail' => 'It is the measurement of the collar.',
                'value' => '32, 34, 36, 38, 40, 42, 44, 46, 48, 50',
            ],
        );

        foreach ($measurekeys as $measurekey) {
            $currentMeasureKey = Measurekey::create([
                'name' => $measurekey['name'],
                'unit' => $measurekey['unit'],
            ]);

            foreach (explode(', ', $measurekey['value']) as $value) {
                $currentMeasureKey->measurevals()->create([
                    'value' => $value,
                    'detail' => $measurekey['detail'],
                ]);
            }
        }

        // Product must be created first

        // $products = Product::all();
        // foreach ($products as $product) {
        //     $attrikeys = Attrikey::all();
        //     foreach ($attrikeys as $attrikey) {
        //         $attrival = $attrikey->attrivals->first();
        //         ProductAttribute::create([
        //             'product_id' => $product->id,
        //             'attrikey_id' => $attrikey->id,
        //             'attrival_id' => $attrival->id,
        //         ]);
        //     }
        //     $measurekeys = Measurekey::all();
        //     foreach ($measurekeys as $measurekey) {
        //         $measureval = $measurekey->measurevals->first();
        //         foreach ($measurekey->measurevals as $measureval) {
        //             ProductMeasurement::updateOrCreate(
        //                 [
        //                     'product_id' => $product->id,
        //                     'measurekey_id' => $measurekey->id,
        //                 ],
        //                 [
        //                     'measureval_id' => $measureval->id,
        //                 ]
        //             );
        //         }
        //     }
        // }

    }
}
