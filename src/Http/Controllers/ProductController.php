<?php

namespace Fpaipl\Prody\Http\Controllers;

use Illuminate\Http\Request;
use Fpaipl\Prody\Models\Product;
use Fpaipl\Panel\Http\Controllers\PanelController;
use Fpaipl\Prody\Http\Requests\ProductEditRequest;
use Fpaipl\Prody\Http\Requests\ProductCreateRequest;
use Fpaipl\Prody\DataTables\ProductDatatable as Datatable;

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
        $product = Product::create($request->validated());
        if (isset($product)) {
            return redirect()->route('products.index')->with('toast', [
                'class' => 'success',
                'text' => $this->messages['create_success']
            ]);
        } else {
            return redirect()->route('products.index')->withInput()->with('toast', [
                'class' => 'danger',
                'text' => $this->messages['create_error']
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
            return redirect()->route('products.edit', $product)->with('toast', [
                'class' => 'success',
                'text' => $this->messages['edit_success']
            ]);
        } catch (\Exception $e) {
            return redirect()->route('products.index')->withInput()->with('toast', [
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
