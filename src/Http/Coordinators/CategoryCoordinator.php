<?php

namespace Fpaipl\Prody\Http\Coordinators;

use Fpaipl\Prody\Models\Product;
use Fpaipl\Prody\Models\Category;
use Illuminate\Support\Facades\Cache;
use Fpaipl\Panel\Http\Coordinators\Coordinator;
use Fpaipl\Prody\Http\Resources\ProductResource;
use Fpaipl\Prody\Http\Resources\CategoryResource;

class CategoryCoordinator extends Coordinator
{
    public $categoryWithChilds = array();
    
    /**
     * Get all categories
     */
    public function index()
    {
        Cache::forget('categories');
        $categories = Cache::remember('categories', 24 * 60 * 60, function () {
            $categories = Category::all();
            return $categories->sortByDesc('order');
        });
        return CategoryResource::collection($categories->values()->all());
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        $category_id = $category->id;
        Cache::forget('products'.$category_id);
        $products = Cache::remember('products'.$category_id, 24 * 60 * 60, function () use($category_id) {
            return Product::with('category')
                ->with('taxation')
                ->with('colors')
                ->where('category_id', $category_id)
                ->whereStatus(Product::STATUS[1])
                ->get();
        });
        return ProductResource::collection($products);
    }


    public function viewall(Category $category){
        $this->getChilds($category);
        Cache::forget('products'.$category->id);
        $products = Cache::remember('products'.$category->id, 24 * 60 * 60, function () {
            return Product::with('category')
                ->with('taxation')
                ->with('colors')
                ->wherein('category_id', $this->categoryWithChilds)
                ->whereStatus(Product::STATUS[1])
                ->get();
        });
        return ProductResource::collection($products);
    }

    private function getChilds(Category $category){
        array_push($this->categoryWithChilds, $category->id);
        if($category->hasChild()){
            foreach($category->child as $child){
                $this->getChilds($child);
            }
        }
    }
}
