<?php

namespace Fpaipl\Prody\Http\Controllers;

use Illuminate\Http\Request;
use Fpaipl\Prody\Models\Brand;
use Fpaipl\Prody\Http\Requests\BrandRequest;
use Fpaipl\Panel\Http\Controllers\PanelController;
use Fpaipl\Prody\Datatables\BrandDatatable as Datatable;

class BrandController extends PanelController
{
    public function __construct()
    {
        parent::__construct(
            new Datatable(), 
            'Fpaipl\Prody\Models\Brand' , 
            'brand', 'brands.index'
        );
    }
   
    public function store(BrandRequest $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:brands',
            'description' => 'nullable|string',
            'image' => 'required|image',
        ]);

        $brand = Brand::create($request->validated());

        if (isset($brand)) {

            $brand
                ->addMedia($request->image)
                ->preservingOriginal()
                ->toMediaCollection(Brand::MEDIA_COLLECTION_NAME);

            return redirect()->route('brands.index')->with('toast', [
                'class' => 'success',
                'text' => $this->messages['create_success']
            ]);

        } else {

            return redirect()->back()->withInput()->with('toast', [
                'class' => 'danger',
                'text' => $this->messages['create_error']
            ]);

        }
    }

    public function update(BrandRequest $request, Brand $brand)
    {
        try {
            $brand->update($request->validated());

            if ($request->hasFile('image')) {
                $brand
                    ->addMedia($request->image)
                    ->preservingOriginal()
                    ->toMediaCollection(Brand::MEDIA_COLLECTION_NAME);
            }

            return redirect()->route('brands.edit', $brand)->with('toast', [
                'class' => 'success',
                'text' => $this->messages['edit_success']
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('toast', [
                'class' => 'danger',
                'text' => $this->messages['edit_error']
            ]);
        }
    }

    public function destroy(Request $request, Brand $brand)
    {
        if ($brand->productsWithTrashed->count() > 0) {
            return redirect()->back()->with('toast', [
                'class' => 'danger',
                'text' => 'Brand cannot be deleted as it has products associated with it.'
            ]);
        }

        try {

            $brand->delete();

            return redirect()->route('brands.index')->with('toast', [
                'class' => 'success',
                'text' => $this->messages['delete_success']
            ]);

        } catch (\Exception $e) {

            return redirect()->back()->with('toast', [
                'class' => 'danger',
                'text' => $this->messages['delete_error']
            ]);

        }
    }
}
