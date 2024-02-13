<?php

namespace Fpaipl\Prody\Http\Livewire;

use Livewire\Component;
use Fpaipl\Shopy\Models\Stock;

/**
 * Class StockUpdate
 *
 * Livewire component for updating the stock quantity of a product.
 *
 * Objectives:
 * 1. To provide an interface for updating the stock quantity of a product.
 * 2. To validate the entered stock quantity before updating.
 * 3. To display validation errors and success messages.
 *
 * Features:
 * 1. Real-time validation for the stock quantity input field.
 * 2. Uses Laravel's validation features for server-side validation.
 * 3. Flash messages for successful or unsuccessful operations.
 *
 */
class StockUpdate extends Component
{
    public int $stockQuantity;
    public $stockId;
    
    public function mount($modelId)
    {
        $this->stockId = $modelId;
        $this->stockQuantity = Stock::find($modelId)->quantity;
    }

    /**
     * Validation rules for the component.
     *
     * @return array
     */
    protected function rules(): array
    {
        return [
            'stockQuantity' => ['required', 'integer', 'min:0'],
        ];
    }

    /**
     * Reset the input fields.
     */
    public function resetInputFields(): void
    {
        $this->stockQuantity = 0;
    }

    /**
     * Update the stock quantity.
     */
    public function updateStock(): void
    {
        $this->validate();

        Stock::updateOrCreate(
            [ 'id' => $this->stockId ],
            [ 'quantity' => $this->stockQuantity ]);

        session()->flash('message', 'Stock quantity has been updated successfully.');

        $this->resetInputFields();
    }

    /**
     * Render the component.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('livewire.stock-update');
    }
}
