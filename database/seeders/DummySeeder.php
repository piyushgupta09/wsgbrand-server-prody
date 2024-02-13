<?php

namespace Fpaipl\Prody\Database\Seeders;

use App\Models\User;
use Fpaipl\Prody\Models\Brand;
use Illuminate\Database\Seeder;
use Fpaipl\Prody\Models\Product;
use Fpaipl\Prody\Models\Category;
use Fpaipl\Prody\Models\Collection;
use Fpaipl\Prody\Models\ProductDecision;

class DummySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        
        /**
         * Create a dummy category.
         */

        $categories = [
            // Top level categories
            [
                'name' => 'Mens',
                'display' => false,
                'children' => [
                    [
                        'name' => 'Jackets',
                        'display' => true,
                    ],
                    [
                        'name' => 'T-Shirts',
                        'display' => true,
                    ],
                    [
                        'name' => 'Pants',
                        'display' => true,
                    ],
                ],
            ],
            [
                'name' => 'Womens',
                'display' => false,
                'children' => [
                    [
                        'name' => 'Jackets',
                        'display' => true,
                    ],
                    [
                        'name' => 'T-Shirts',
                        'display' => true,
                    ],
                    [
                        'name' => 'Dresses',
                        'display' => true,
                    ],
                ],
            ],
            [
                'name' => 'Kids',
                'display' => false,
                'children' => [
                    [
                        'name' => 'Jackets',
                        'display' => true,
                    ],
                    [
                        'name' => 'T-Shirts',
                        'display' => true,
                    ],
                    [
                        'name' => 'Dresses',
                        'display' => true,
                    ],
                ],
            ],
            [
                'name' => 'Accessories',
                'display' => false,
                'children' => [
                    [
                        'name' => 'Belts',
                        'display' => false,
                    ],
                ],
            ],
        ];

        foreach ($categories as $category) {
            $newCategory = Category::create([
                'name' => $category['name'],
                'display' => $category['display'],
            ]);

            $newCategory->addMedia(storage_path('app/public/assets/placeholders/default.jpg'))->preservingOriginal()->toMediaCollection(Category::MEDIA_COLLECTION_NAME);

            if (isset($category['children'])) {
                foreach ($category['children'] as $child) {
                    $newChild = Category::create([
                        'name' => $child['name'],
                        'display' => $child['display'],
                        'parent_id' => $newCategory->id,
                    ]);

                    $newChild->addMedia(storage_path('app/public/assets/placeholders/default.jpg'))->preservingOriginal()->toMediaCollection(Category::MEDIA_COLLECTION_NAME);
                }
            }
        }

        /**
         * Create a dummy brand.
         */

        $brands = [
            ['name' => 'Desigal',],
        ];

        foreach ($brands as $brand) {
            $newBrand = \Fpaipl\Prody\Models\Brand::create([
                'name' => $brand['name']
            ]);
            $newBrand->addMedia(storage_path('app/public/assets/placeholders/default.jpg'))->preservingOriginal()->toMediaCollection(Brand::MEDIA_COLLECTION_NAME);
        }




        $products = [
            [
                'name' => 'Top Summer',
                'code' => '20001',
                'brand_id' => 1,
                'category_id' => 1,
                'tax_id' => 1,
                'moq' => 10,
            ],
            [
                'name' => 'Red Jacket',
                'code' => '20011',
                'brand_id' => 1,
                'category_id' => 1,
                'tax_id' => 1,
                'moq' => 10,
            ],
            [
                'name' => 'Black Jacket',
                'code' => '20012',
                'brand_id' => 1,
                'category_id' => 1,
                'tax_id' => 1,
                'moq' => 10,
            ],
            [
                'name' => 'Blue Jacket',
                'code' => '20013',
                'brand_id' => 1,
                'category_id' => 1,
                'tax_id' => 1,
                'moq' => 10,
            ],
            [
                'name' => 'Green Jacket',
                'code' => '20014',
                'brand_id' => 1,
                'category_id' => 1,
                'tax_id' => 1,
                'moq' => 10,
            ],
            [
                'name' => 'Yellow Jacket',
                'code' => '20015',
                'brand_id' => 1,
                'category_id' => 1,
                'tax_id' => 1,
                'moq' => 10,
            ],
            [
                'name' => 'White Jacket',
                'code' => '20016',
                'brand_id' => 1,
                'category_id' => 1,
                'tax_id' => 1,
                'moq' => 10,
            ],
            [
                'name' => 'Black T-Shirt',
                'code' => '20021',
                'brand_id' => 1,
                'category_id' => 1,
                'tax_id' => 1,
                'moq' => 10,
            ],
            [
                'name' => 'White T-Shirt',
                'code' => '20022',
                'brand_id' => 1,
                'category_id' => 1,
                'tax_id' => 1,
                'moq' => 10,
            ],
            [
                'name' => 'Blue T-Shirt',
                'code' => '20023',
                'brand_id' => 1,
                'category_id' => 1,
                'tax_id' => 1,
                'moq' => 10,
            ],
        ];

        foreach ($products as $product) {
            $newProduct = Product::create([
                'name' => $product['name'],
                'code' => $product['code'],
                'brand_id' => $product['brand_id'],
                'category_id' => $product['category_id'],
                'tax_id' => $product['tax_id'],
                'moq' => $product['moq'],
            ]);

            ProductDecision::create([
                'product_id' => $newProduct->id,
            ]);
        }

        /*
         * Create a dummy collections.
         */

        $featuredCollections = [
            ['name' => 'New Arrivals'],
            ['name' => 'Buy One Get One'],
            ['name' => 'Upto 50% Off'],
            ['name' => 'Stock Clearance'],
        ];

        foreach ($featuredCollections as $index => $featuredCollection) {

            $newCollection = Collection::create([
                'name' => $featuredCollection['name'],
                'info' => $featuredCollection['name'],
                'order' => $index + 1,
                'type' => 'featured',
            ]);

            $newCollection->addMedia(storage_path('app/public/assets/placeholders/default.jpg'))->preservingOriginal()->toMediaCollection(Collection::MEDIA_COLLECTION_NAME);

        }
    }
}
