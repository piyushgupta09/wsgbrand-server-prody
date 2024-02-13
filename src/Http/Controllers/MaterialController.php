<?php

namespace Fpaipl\Prody\Http\Controllers;

use Illuminate\Http\Request;
use Fpaipl\Prody\Models\Material;
use Fpaipl\Panel\Http\Controllers\PanelController;
use Fpaipl\Prody\Http\Requests\MaterialRequest;
use Fpaipl\Prody\Datatables\MaterialDatatable as Datatable;

class MaterialController extends PanelController
{
    public function __construct()
    {
        parent::__construct(new Datatable(), 'Fpaipl\Prody\Models\Material' , 'material', 'materials.index');
    }
   
    public function store(MaterialRequest $request)
    {
        $material = Material::create($request->validated());
        if (isset($material)) {
            return redirect()->route('materials.index')->with('toast', [
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

    public function update(MaterialRequest $request, Material $material)
    {
        try {
            $material->update($request->validated());
            return redirect()->route('materials.edit', $material)->with('toast', [
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

    public function destroy(Request $request, Material $material)
    {
        $response = Material::safeDeleteModels(
            array($material->id), 
            'App\Models\Material'
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

        return redirect()->route('materials.index');
    }
}
