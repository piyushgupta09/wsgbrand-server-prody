<?php

namespace Fpaipl\Prody\Http\Controllers;

use Illuminate\Http\Request;
use Fpaipl\Prody\Models\Collection;
use Fpaipl\Panel\Http\Controllers\PanelController;
use Fpaipl\Prody\Http\Requests\CollectionEditRequest;
use Fpaipl\Prody\Http\Requests\CollectionCreateRequest;
use Fpaipl\Prody\Datatables\CollectionDatatable as Datatable;

class CollectionController extends PanelController
{

    public function __construct()
    {
        parent::__construct(new Datatable(), 'Fpaipl\Prody\Models\Collection' , 'collection', 'collections.index');
    }
   
    // Database Affecting Function

    /**
     * Store a newly created resource in storage.
     */
    public function store(CollectionCreateRequest $request)
    {
        $collection = Collection::create($request->validated());

        if (isset($collection)) {

            $collection->addMultipleMediaToModel(
                $request->hasFile('images') ? $request->images : array()
            );

            return redirect()->route('collections.index')->with('toast', [
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

    /**
     * Update the specified resource in storage.
     */
    public function update(CollectionEditRequest $request, Collection $collection)
    {
        try {

            $collection->update($request->validated());

            $collection->addMultipleMediaToModel(
                $request->hasFile('images') ? $request->images : array()
            );

            $collection->removeMedia(
                $request->has('delete_images') ? $request->delete_images : array()
            );

            return redirect()->route('collections.edit', $collection)->with('toast', [
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

    public function destroy(Request $request, Collection $collection)
    {
        $response = Collection::safeDeleteModels(
            array($collection->id), 
            'App\Models\Collection'
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

        return redirect()->route('collections.index');
    }
}
