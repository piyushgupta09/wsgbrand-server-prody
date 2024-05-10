<?php

namespace Fpaipl\Prody\Console\Commands;

use Illuminate\Console\Command;
use Database\Factories\ProductFactory;
use Illuminate\Support\Facades\DB;

class SeedProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     * @example php artisan prody:seed-products 10
     */
    protected $signature = 'prody:seed-products {count}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed the database with the specified number of products';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        DB::beginTransaction();

        try {
            $count = (int) $this->argument('count');
            if ($count < 1) {
                $this->error('The count must be at least 1.');
                return 1; // Return error code
            }
    
            $products = ProductFactory::new()->count($count)->create();
            foreach ($products as $product) {
                if (!$product) {
                    $this->error('Failed to create a product.');
                    continue;
                }
                $product->addToCollection('recommended');
                $this->info("Created product with ID: {$product->id}, Name: {$product->name}");
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("An error occurred: " . $e->getMessage());
            return 1; // Return error code
        }
    
        return 0; // Success
    }
    
}
