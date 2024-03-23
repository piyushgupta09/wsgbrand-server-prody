<?php

namespace Fpaipl\Prody\Http\Controllers;

use Fpaipl\Prody\Models\ReturnPolicy;
use Illuminate\Http\Request;
use Fpaipl\Panel\Http\Controllers\PanelController;
use Fpaipl\Prody\Datatables\ReturnPolicyDatatable as Datatable;

class ReturnPolicyController extends PanelController
{
    public function __construct()
    {
        parent::__construct(
            new Datatable(), 
            'Fpaipl\Prody\Models\ReturnPolicy' , 
            'return_policy', 'return-policies.index'
        );
    }
   
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|unique:return_policies,name',
            'abbr' => 'required|unique:return_policies,abbr',
        ]);

        ReturnPolicy::create([
            'name' => $request->name,
            'names' => $request->names ? $request->names : $request->name,
            'abbr' => $request->abbr,
            'abbrs' => $request->abbrs ? $request->abbrs : $request->abbr,
        ]);

        return redirect()->route('return-policies.index')->with('toast', [
            'class' => 'success',
            'text' => 'Discount created successfully'
        ]); 
    }

    public function update(Request $request, ReturnPolicy $returnPolicy)
    {
        $this->validate($request, [
            'name' => 'required|unique:return_policies,name,' . $returnPolicy->id,
            'abbr' => 'required|unique:return_policies,abbr,' . $returnPolicy->id,
        ]);

        $returnPolicy->name = $request->name;
        $returnPolicy->names = $request->names ? $request->names : $request->name;
        $returnPolicy->abbr = $request->abbr;
        $returnPolicy->abbrs = $request->abbrs ? $request->abbrs : $request->abbr;
        $returnPolicy->save();

        return redirect()->route('return-policies.index')->with('toast', [
            'class' => 'success',
            'text' => 'Discount updated successfully.'
        ]);
    }

    public function destroy(ReturnPolicy $returnPolicy)
    {
        $returnPolicy->delete();

        return redirect()->route('return-policies.index')->with('toast', [
            'class' => 'success',
            'text' => 'Discount deleted successfully.'
        ]);
    }
}
