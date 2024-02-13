<?php

namespace Fpaipl\Prody\Database\Seeders;

use Illuminate\Database\Seeder;
use Fpaipl\Prody\Models\Supplier;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $suppliers = [
            [
                'name' => 'Monaal Creation',
                'address' => 'B-74, 1st Floor, Okhla Industrial Area, Phase II, New Delhi, Delhi 110020',
                'contact_person' => 'Mr. Shubash Kumar',
                'contact_number' => '9999437620',
                'email' => 'info@monaal.in',
                'website' => config('monaal.url'),
                'type' => 'material-supplier',
            ],
            [
                'name' => 'General Supplier',
                'address' => 'Open Market',
                'contact_person' => 'General Supplier',
                'contact_number' => '',
                'email' => '',
                'website' => '',
                'type' => 'general-supplier',
            ],
        ];

        foreach ($suppliers as $supplier) {
            $newSupplier = Supplier::create([
                    'name' => $supplier['name'],
                    'address' => $supplier['address'],
                    'contact_person' => $supplier['contact_person'],
                    'contact_number' => $supplier['contact_number'],
                    'email' => $supplier['email'],
                    'website' => $supplier['website'],
                    'type' => $supplier['type'],
            ]);

            $newSupplier
                ->addMedia(storage_path('app/public/assets/placeholders/default.jpg'))
                ->preservingOriginal()
                ->toMediaCollection(Supplier::MEDIA_COLLECTION_NAME);
        }

    }
}
