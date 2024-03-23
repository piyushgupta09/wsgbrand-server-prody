<?php

namespace Fpaipl\Prody\Http\Controllers;

use Fpaipl\Prody\Models\Discount;
use Illuminate\Http\Request;
use Fpaipl\Panel\Http\Controllers\PanelController;
use Fpaipl\Prody\Datatables\DiscountDatatable as Datatable;

class DiscountController extends PanelController
{
    public function __construct()
    {
        parent::__construct(
            new Datatable(), 
            'Fpaipl\Prody\Models\Discount' , 
            'discount', 'discounts.index'
        );
    }
   
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|unique:discounts,name',
            'value' => 'required|numeric',
            'type' => 'required|in:percentage,amount',
            'details' => 'nullable',
        ], [
            'type.in' => 'The type field must be either percentage or amount.',
            'value.numeric' => 'The value field must be a number.',
            'name.unique' => 'The name field must be unique.',
        ]); 

        Discount::create([
            'name' => $request->name,
            'value' => $request->value,
            'type' => $request->type,
            'details' => $request->details,
            'active' => $request->active ? 1 : 0,
        ]);

        return redirect()->route('discounts.index')->with('toast', [
            'class' => 'success',
            'text' => 'Discount created successfully'
        ]); 
    }

    public function update(Request $request, Discount $discount)
    {
        $this->validate($request, [
            'name' => 'required|unique:discounts,name,' . $discount->id,
            'abbr' => 'required|unique:discounts,abbr,' . $discount->id,
        ]);

        $discount->name = $request->name;
        $discount->names = $request->names ? $request->names : $request->name;
        $discount->abbr = $request->abbr;
        $discount->abbrs = $request->abbrs ? $request->abbrs : $request->abbr;
        $discount->save();

        return redirect()->route('discounts.index')->with('toast', [
            'class' => 'success',
            'text' => 'Discount updated successfully.'
        ]);
    }

    public function destroy(Discount $discount)
    {
        $discount->delete();

        return redirect()->route('discounts.index')->with('toast', [
            'class' => 'success',
            'text' => 'Discount deleted successfully.'
        ]);
    }
}
