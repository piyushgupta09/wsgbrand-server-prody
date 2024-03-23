<?php

namespace Fpaipl\Prody\Database\Seeders;

use Illuminate\Database\Seeder;
use Fpaipl\Prody\Models\Discount;
use Fpaipl\Prody\Models\RefundPolicy;
use Fpaipl\Prody\Models\Strategy;
use Illuminate\Support\Facades\DB;
use Fpaipl\Prody\Models\ReturnPolicy;

class DatasetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $overheads = array(
            [
                "stage" => "production",
                "name" => "Fabrication - Fab 2",
                "amount" => "25.00",
                "capacity" => "1.00",
                "rate" => "25.00",
                "details" => "Per Catalog",
            ],
            [
                "stage" => "production",
                "name" => "Fabrication - Fab 3",
                "amount" => "25.00",
                "capacity" => "1.00",
                "rate" => "25.00",
                "details" => "Per Catalog",
            ],
            [
                "stage" => "production",
                "name" => "Fabrication - Fab 5",
                "amount" => "25.00",
                "capacity" => "1.00",
                "rate" => "25.00",
                "details" => "Per Catalog",
            ],
            [
                "stage" => "production",
                "name" => "Fabrication - Fab 1",
                "amount" => "25.00",
                "capacity" => "1.00",
                "rate" => "25.00",
                "details" => "Per Catalog",
            ],
            [
                "stage" => "production",
                "name" => "Fabrication - Sardar",
                "amount" => "30.00",
                "capacity" => "1.00",
                "rate" => "30.00",
                "details" => "Per Catalog",
            ],
            [
                "stage" => "production",
                "name" => "Fabrication - Dnb",
                "amount" => "28.00",
                "capacity" => "1.00",
                "rate" => "28.00",
                "details" => "Per Catalog",
            ],
            [
                "stage" => "production",
                "name" => "Fabrication - Fab 4",
                "amount" => "25.00",
                "capacity" => "1.00",
                "rate" => "25.00",
                "details" => "Per Catalog",
            ],
            [
                "stage" => "production",
                "name" => "Fabrication - Fab 6",
                "amount" => "25.00",
                "capacity" => "1.00",
                "rate" => "25.00",
                "details" => "Per Catalog",
            ],
            [
                "stage" => "sales",
                "name" => "Shop Salary Staff",
                "amount" => "1200000.00",
                "capacity" => "100000.00",
                "rate" => "12.00",
                "details" => "ecfc",
            ],
        );

        foreach ($overheads as $overhead) {
            DB::table('overheads')->insert([
                "stage" => $overhead['stage'],
                "name" => $overhead['name'],
                "amount" => $overhead['amount'],
                "capacity" => $overhead['capacity'],
                "rate" => $overhead['rate'],
                "details" => $overhead['details'],
            ]);
        }

        $consumables = array(
            [
                "name" => "Fusing",
                "unit" => "Per Piece",
                "rate" => "1.00",
                "details" => "Variable",
            ],
            [
                "name" => "Label Tag",
                "unit" => "Per Piece",
                "rate" => "5.00",
                "details" => "Non-Variable",
            ],
            [
                "name" => "Gatta",
                "unit" => "Per Piece",
                "rate" => "2.00",
                "details" => "Non-Variable",
            ],
            [
                "name" => "Patch",
                "unit" => "Per Piece",
                "rate" => "1.00",
                "details" => "Variable",
            ],
            [
                "name" => "Belt",
                "unit" => "Per Piece",
                "rate" => "1.25",
                "details" => "Variable",
            ],
            [
                "name" => "Button",
                "unit" => "Per Piece",
                "rate" => "1.00",
                "details" => "Variable",
            ],
            [
                "name" => "Sticker",
                "unit" => "Per Piece",
                "rate" => "1.00",
                "details" => "Variable",
            ],
            [
                "name" => "Zipper",
                "unit" => "Per Piece",
                "rate" => "1.00",
                "details" => "Variable",
            ],
            [
                "name" => "Dori",
                "unit" => "Per Piece",
                "rate" => "1.00",
                "details" => "Variable",
            ],
            [
                "name" => "Elastic",
                "unit" => "Per Piece",
                "rate" => "1.00",
                "details" => "Variable",
            ],
            [
                "name" => "Hook",
                "unit" => "Per Piece",
                "rate" => "1.00",
                "details" => "Variable",
            ],
            [
                "name" => "Buckle",
                "unit" => "Per Piece",
                "rate" => "1.00",
                "details" => "Variable",
            ],
            [
                "name" => "Embroidery",
                "unit" => "Per Catalog",
                "rate" => "1.00",
                "details" => "Variable",
            ],
            [
                "name" => "Step Shoulder",
                "unit" => "Per Catalog",
                "rate" => "1.00",
                "details" => "Variable",
            ],
            [
                "name" => "Single Lock",
                "unit" => "Per 100 Stitch",
                "rate" => "70.00",
                "details" => "Non-Variable",
            ],
            [
                "name" => "Overlock",
                "unit" => "Per 100 Stitch",
                "rate" => "50.00",
                "details" => "Non-Variable",
            ],
            [
                "name" => "Kaj",
                "unit" => "Per 10 Count",
                "rate" => "7.00",
                "details" => "Non-Variable",
            ],
            [
                "name" => "Piko",
                "unit" => "Per Stitch",
                "rate" => "1.00",
                "details" => "Variable",
            ],
            [
                "name" => "Smoking (Kansai)",
                "unit" => "Per 10 Meter",
                "rate" => "6.00",
                "details" => "Non-Variable",
            ],
        );

        foreach ($consumables as $consumable) {
            DB::table('consumables')->insert([
                'name' => $consumable['name'],
                'unit' => $consumable['unit'],
                'rate' => $consumable['rate'],
                'details' => $consumable['details'],
            ]);
        }

        // Product Pricing Strategy

        $pricingStrategy = array(
            [
                'name' => 'Base Price',
                'math' => 'add',
                'value' => 0,
                'type' => 'percentage',
                'details' => 'No adjustment',
                'active' => 1,
            ],
            [
                'name' => 'Discounted Price',
                'math' => 'less',
                'value' => 10,
                'type' => 'percentage',
                'details' => '10% discount on 100+ units',
            ],
            [
                'name' => 'Premium Price',
                'math' => 'add',
                'value' => 20,
                'type' => 'percentage',
                'details' => '20% extra on premium products',
            ]
        );

        foreach ($pricingStrategy as $strategy) {
            Strategy::create([
                'name' => $strategy['name'],
                'math' => $strategy['math'],
                'value' => $strategy['value'],
                'type' => $strategy['type'],
                'details' => $strategy['details'],
            ]);
        }

        // Discount Strategy

        $discounts = array(
            [
                'name' => 'No Discount',
                'value' => 0,
                'type' => 'percentage',
                'details' => 'No discount',
            ],
            [
                'name' => 'Big Billions Day',
                'value' => 20,
                'type' => 'percentage',
                'details' => '20% discount on order',
                'multi_time' => true,
                'on_total' => true,
                'on_checkout' => true,
                'min_total' => 25000,
                'max_total' => 100000,
            ],
            [
                'name' => 'Membership Discount',
                'value' => 5,
                'type' => 'percentage',
                'details' => '5% discount on order for members',
                'multi_time' => true,
                'on_total' => true,
                'on_checkout' => true,
                'min_total' => 10000,
                'max_total' => 1000000,
            ],
        );

        foreach ($discounts as $discount) {
            Discount::create([
                'name' => $discount['name'],
                'value' => $discount['value'],
                'type' => $discount['type'],
                'details' => $discount['details'],
                'one_time' => isset($discount['one_time']) ? $discount['one_time'] : false,
                'multi_time' => isset($discount['multi_time']) ? $discount['multi_time'] : false,
                'on_total' =>  isset($discount['on_total']) ? $discount['on_total'] : false,
                'on_checkout' => isset($discount['on_checkout']) ? $discount['on_checkout'] : false,
                'min_total' =>isset($discount['min_total']) ? $discount['min_total'] : 0,
                'max_total' => isset($discount['max_total']) ? $discount['max_total'] : 0,
            ]);
        }

        // Return Policy

        $returnPolicy = array(
            [
                'name' => 'No Return',
                'details' => 'No return policy',
                'active' => 1,
            ],
            [
                'name' => '7 Days Return',
                'details' => '7 days return policy',
                'active' => 1,
            ],
            [
                'name' => '15 Days Return',
                'details' => '15 days return policy',
                'active' => 1,
            ],
            [
                'name' => '30 Days Return',
                'details' => '30 days return policy',
                'active' => 1,
            ],
        );

        foreach ($returnPolicy as $policy) {
            ReturnPolicy::create([
                'name' => $policy['name'],
                'details' => $policy['details'],
                'active' => $policy['active'],
            ]);
        }

        // Refund Policy

        $refundPolicy = array(
            [
                'name' => 'No Refund',
                'details' => 'No refund policy',
                'active' => 1,
            ],
            [
                'name' => '7 Days Refund',
                'details' => '7 days refund policy',
                'active' => 1,
            ],
            [
                'name' => '15 Days Refund',
                'details' => '15 days refund policy',
                'active' => 1,
            ],
            [
                'name' => '30 Days Refund',
                'details' => '30 days refund policy',
                'active' => 1,
            ],
        );

        foreach ($refundPolicy as $policy) {
            RefundPolicy::create([
                'name' => $policy['name'],
                'details' => $policy['details'],
                'active' => $policy['active'],
            ]);
        }

    }
}
