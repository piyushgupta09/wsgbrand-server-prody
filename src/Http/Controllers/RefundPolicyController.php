<?php

namespace Fpaipl\Prody\Http\Controllers;

use Fpaipl\Prody\Models\RefundPolicy;
use Illuminate\Http\Request;
use Fpaipl\Panel\Http\Controllers\PanelController;
use Fpaipl\Prody\Datatables\RefundPolicyDatatable as Datatable;

class RefundPolicyController extends PanelController
{
    public function __construct()
    {
        parent::__construct(
            new Datatable(), 
            'Fpaipl\Prody\Models\RefundPolicy' , 
            'refund_policy', 'refund-policies.index'
        );
    }
   
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|unique:refund_policies,name',
            'abbr' => 'required|unique:refund_policies,abbr',
        ]);

        RefundPolicy::create([
            'name' => $request->name,
            'names' => $request->names ? $request->names : $request->name,
            'abbr' => $request->abbr,
            'abbrs' => $request->abbrs ? $request->abbrs : $request->abbr,
        ]);

        return redirect()->route('refund-policies.index')->with('toast', [
            'class' => 'success',
            'text' => 'Discount created successfully'
        ]); 
    }

    public function update(Request $request, RefundPolicy $refundPolicy)
    {
        $this->validate($request, [
            'name' => 'required|unique:refund_policies,name,' . $refundPolicy->id,
            'abbr' => 'required|unique:refund_policies,abbr,' . $refundPolicy->id,
        ]);

        $refundPolicy->name = $request->name;
        $refundPolicy->names = $request->names ? $request->names : $request->name;
        $refundPolicy->abbr = $request->abbr;
        $refundPolicy->abbrs = $request->abbrs ? $request->abbrs : $request->abbr;
        $refundPolicy->save();

        return redirect()->route('refund-policies.index')->with('toast', [
            'class' => 'success',
            'text' => 'Discount updated successfully.'
        ]);
    }

    public function destroy(RefundPolicy $refundPolicy)
    {
        $refundPolicy->delete();

        return redirect()->route('refund-policies.index')->with('toast', [
            'class' => 'success',
            'text' => 'Discount deleted successfully.'
        ]);
    }
}
