<?php

namespace Fpaipl\Prody\Database\Seeders;

use Fpaipl\Prody\Models\Tax;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatasetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $taxes = [
            ['hsncode' => '600490', 'rate' => '5'],
            ['hsncode' => '600510', 'rate' => '5'],
            ['hsncode' => '600523', 'rate' => '5'],
            ['hsncode' => '600631', 'rate' => '5'],
            ['hsncode' => '600632', 'rate' => '5'],
            ['hsncode' => '600690', 'rate' => '5'],
            ['hsncode' => '610310', 'rate' => '5'],
            ['hsncode' => '630210', 'rate' => '5'],
            ['hsncode' => '680221', 'rate' => '5'],
            ['hsncode' => '60051000', 'rate' => '5'],
            ['hsncode' => '60052400', 'rate' => '5'],
            ['hsncode' => '60063200', 'rate' => '5'],
            ['hsncode' => '60069000', 'rate' => '5'],
        ];

        foreach ($taxes as $tax) {
            Tax::create([
                'name' => $tax['hsncode'] . ' - GST ' . $tax['rate'] . '%',
                'hsncode' => $tax['hsncode'],
                'gstrate' => $tax['rate'],
            ]);
        }

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

    }
}
