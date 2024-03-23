<?php

namespace Fpaipl\Prody\Http\Controllers;

use Illuminate\Http\Request;
use Fpaipl\Prody\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Fpaipl\Panel\Http\Controllers\PanelController;
use Fpaipl\Prody\Http\Requests\ProductEditRequest;
use Fpaipl\Prody\Http\Requests\ProductCreateRequest;
use Fpaipl\Prody\Datatables\ProductDatatable as Datatable;

class ProductController extends PanelController
{

    public function __construct()
    {
        parent::__construct(
            new Datatable(), 
            'Fpaipl\Prody\Models\Product', 
            'product', 'products.index'
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductCreateRequest $request)
    {
        DB::beginTransaction();

        try {
            $product = Product::create($request->validated());
            
            if (!isset($product)) {
                throw new \Exception('Product creation failed.');
            }

            // If withrelations is set, duplicate the related records
            if ($request->has('withrelations')) {

                // validate if the po with id exists in the database
                $this->validate($request, [
                    'withrelations' => 'exists:products,slug'
                ], [
                    'withrelations.exists' => 'Product with id ' . $request->input('withrelations') . ' does not exist'
                ]);

                $existingProduct = Product::where('slug', $request->input('withrelations'))->first();
                $existingProduct->duplicateRelations($product);                
            }

            DB::commit();

            return redirect()->route('products.show', $product->slug)->with('toast', [
                'class' => 'success',
                'text' => 'Product created successfully.'
            ]);

        } catch (\Exception $e) {
            
            Log::info($e->getMessage());
            DB::rollBack();

            return redirect()->route('products.index')->withInput()->with('toast', [
                'class' => 'danger',
                'text' => 'Something went wrong. Please try again.'
            ]);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProductEditRequest $request, Product $product)
    {
        try {
            $product->update($request->validated());
            return redirect()->route('products.show', $product->slug)->with('toast', [
                'class' => 'success',
                'text' => $this->messages['edit_success']
            ]);
        } catch (\Exception $e) {
            return redirect()->route('products.show', $product->slug)->withInput()->with('toast', [
                'class' => 'danger',
                'text' => $this->messages['edit_error']
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Product $product)
    {
        $response = Product::safeDeleteModels(
            array($product->id), 
            'App\Models\Product'
        );

        switch ($response) {
            case 'dependent':
                session()->flash('toast', [
                    'class' => 'danger',
                    'text' => $this->messages['has_dependency']
                ]);
                break;
            case 'success':
                session()->flash('toast', [
                    'class' => 'success',
                    'text' => $this->messages['delete_success']
                ]);
                break;    
            default: // failure
                session()->flash('toast', [
                    'class' => 'danger',
                    'text' => $this->messages['delete_error']
                ]);
                break;
        }

        return redirect()->route('products.index');
    }
}
