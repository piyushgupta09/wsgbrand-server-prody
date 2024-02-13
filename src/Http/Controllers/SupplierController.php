<?php

namespace Fpaipl\Prody\Http\Controllers;

use Illuminate\Http\Request;
use Fpaipl\Prody\Models\Supplier;
use Fpaipl\Panel\Http\Controllers\PanelController;
use Fpaipl\Prody\Http\Requests\SupplierRequest;
use Fpaipl\Prody\DataTables\SupplierDatatable as Datatable;

class SupplierController extends PanelController
{
    public function __construct()
    {
        parent::__construct(new Datatable(), 'Fpaipl\Prody\Models\Supplier' , 'supplier', 'suppliers.index');
    }
   
    /**
     * Store a newly created resource in storage.
     */
    public function store(SupplierRequest $request)
    {
        $supplier = Supplier::create($request->validated());

        if ($request->hasFile('image')) {
            $supplier
                ->addMedia($request->image)
                ->preservingOriginal()
                ->toMediaCollection(Supplier::MEDIA_COLLECTION_NAME);
        }

        return redirect()->route('suppliers.index')->with('toast', [
            'class' => 'success',
            'text' => 'Supplier created successfully.'
        ]);
    }

    public function update(SupplierRequest $request, Supplier $supplier)
    {
        $supplier->update($request->validated());

        if ($request->hasFile('image')) {
            $supplier
                ->addMedia($request->image)
                ->preservingOriginal()
                ->toMediaCollection(Supplier::MEDIA_COLLECTION_NAME);
        }

        return redirect()->route('suppliers.index')->with('toast', [
            'class' => 'success',
            'text' => 'Supplier updated successfully.'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Supplier $supplier)
    {
        if ($supplier->materials()->count() > 0) {
            return redirect()->route('suppliers.index')->with('toast', [
                'class' => 'danger',
                'text' => 'Supplier cannot be deleted as it is attached to one or more materials.'
            ]);
        }

        $supplier->delete();

        return redirect()->route('suppliers.index')->with('toast', [
            'class' => 'success',
            'text' => 'Supplier deleted successfully.'
        ]);
    }
}
