<?php

namespace Fpaipl\Prody\Http\Livewire;

use Livewire\Component;
use Fpaipl\Prody\Models\Material;
use Fpaipl\Prody\Models\MaterialRange;

/**
 * This Livewire component handles color options and images related to materials.
 */
class MaterialRanges extends Component
{
    public $showForm;

    // Variables to hold form data
    public $materialId;
    public $material;
    public $matRanges;

    public $formType;

    public $materialRangeWidth;
    public $materialRangeLength;
    public $materialRangeRate;

    public $materialRangeId;

    public function mount($modelId)
    {
        $this->materialId = $modelId;
        $this->showForm = false;
        $this->material = Material::findOrFail($modelId);
        $this->resetForm();
    }

    public function toggleForm()
    {
        $this->showForm = !$this->showForm;
    }

    public function resetForm()
    {
        $this->materialRangeWidth = '';
        $this->materialRangeLength = '';
        $this->materialRangeRate = '';
        $this->formType = 'create';
        $this->reloadData();
    }

    public function reloadData()
    {
        $this->matRanges = $this->material->materialRanges;
    }

    public function save()
    {
        // Validate form inputs
        $this->validate([
            'materialRangeWidth' => ['required', 'numeric', 'min:1'],
            'materialRangeLength' => ['nullable', 'string', 'min:1', 'max:255'],
            'materialRangeRate' => ['required', 'string', 'min:3', 'max:255'],
        ]);

        // Either find or create
        $this->getMaterialRange();

        if ($this->formType == 'create') {
            $action = 'created';
        } else {
            $action = 'updated';
        }

       return redirect()->route('materials.show', $this->materialId)->with('toast', [
            'class' => 'success',
            'text' => 'Material Range is ' . $action . ' successfully.'
       ]);
    }

    public function edit($materialRangeId)
    {
        // Set the material option id to be used in the update() method
        $this->materialRangeId = $materialRangeId;
        // Find the material option
        $materialRange = MaterialRange::findOrFail($materialRangeId);
        $this->materialRangeWidth = $materialRange->width;
        $this->materialRangeLength = $materialRange->length;
        $this->materialRangeRate = $materialRange->rate;
        $this->formType = 'edit';
        $this->showForm = true;
    }

    public function delete($materialRangeId)
    {
        $materialRange = MaterialRange::find($materialRangeId);
        $materialRange->forceDelete();
        return redirect()->route('materials.show', $this->materialId)->with('toast', [
            'class' => 'success',
            'text' => 'Material Range is deleted successfully.'
        ]);
    }

    /**
     * Finds an existing size range by material_id and width, or creates a new one.
     * It updates the record if it exists, or creates a new one with the given attributes.
     *
     * @return \Fpaipl\Prody\Models\MaterialRange
     * @throws \Exception If properties are not set or validation fails
     */
    private function getMaterialRange()
    {
        // Example validation, adjust according to your requirements
        if (is_null($this->materialId) || is_null($this->materialRangeWidth)) {
            throw new \Exception('Required properties for MaterialRange are not set.');
        }

        try {
            // Find existing record or instantiate a new one
            $materialRange = MaterialRange::firstOrNew([
                'material_id' => $this->materialId,
                'width' => $this->materialRangeWidth
            ]);

            // Set or update other attributes
            $materialRange->length = $this->materialRangeLength;
            $materialRange->rate = $this->materialRangeRate;

            // Save the record
            $materialRange->save();

            return $materialRange;
        } catch (\Exception $e) {
            // Handle or log the exception as required
            throw $e;
        }
    }

    /**
     * Renders the component
     */
    public function render()
    {
        return view('prody::livewire.material-ranges');
    }
}
