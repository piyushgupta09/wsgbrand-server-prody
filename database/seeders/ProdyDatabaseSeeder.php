<?php

namespace Fpaipl\Prody\Database\Seeders;

use Attribute;
use Illuminate\Database\Seeder;
use Fpaipl\Prody\Database\Seeders\DummySeeder;
use Fpaipl\Prody\Database\Seeders\DatasetSeeder;
use Fpaipl\Prody\Database\Seeders\SupplierSeeder;
use Fpaipl\Prody\Database\Seeders\AttributeSeeder;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ProdyDatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
    */
    public function run(): void
    {
        $this->call(DatasetSeeder::class);
        $this->call(SupplierSeeder::class);
        $this->call(DummySeeder::class);
        $this->call(AttributeSeeder::class);
    }
}
