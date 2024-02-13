<?php

namespace Fpaipl\Prody\Http\Livewire;

use Fpaipl\Prody\Models\Product;
use Livewire\Component;
use Fpaipl\Prody\Models\CollectionProduct;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class CollectionProducts
 *
 * Livewire component for managing products within a collection.
 *
 * Objectives:
 * 1. To provide an interface for associating products with a specific collection.
 * 2. To allow for the selection of product options (e.g., colors) for each product in the collection.
 * 3. To offer real-time updates for product options based on the selected product.
 * 4. To validate the selected product and its options before storing them.
 * 5. To create or update the product's association with the collection.
 * 6. To delete a product from the collection.
 * 7. To display a list of all products currently associated with the collection.
 *
 * Features:
 * 1. Real-time product option updates based on selected product.
 * 2. Validation for ensuring that a product and its options are selected before storing.
 * 3. Flash messages for successful or unsuccessful operations.
 * 4. Utilizes Laravel Eloquent for database interactions.
 * 5. Uses `updateOrCreate` method for efficient database operations.
 * 6. Reset functionality to clear the selected product and its options.
 *
 */
class CollectionProducts extends Component
{
    public $cProducts;
    public int $collectionId;
    public $allProducts;
    public $selectedProduct;
    public $productOptions;
    public $selectedProductOption;

    private const SUCCESS_MESSAGE = 'Collection product has been created successfully.';
    private const DELETE_MESSAGE = 'Collection product has been deleted successfully.';
    private const ERROR_MESSAGE = 'Some issue occurred.';

    /**
     * Initialize the component.
     *
     * @param int $modelId
     */
    public function mount(int $modelId): void
    {
        $this->collectionId = $modelId;
        $this->cProducts = new Collection(); 
        $this->allProducts = Product::all();
        $this->productOptions = collect();
    }

    /**
     * Update product options when a product is selected.
     *
     * @param mixed $value
     */
    public function updatedSelectedProduct($value): void
    {
        if ($value == null) {
            $this->productOptions = collect();
            return;
        }
        $this->productOptions = Product::find($value)->productOptions;
    }

    /**
     * Delete a collection product.
     *
     * @param int $id
     */
    public function delete(int $id): void
    {
        if (CollectionProduct::findOrFail($id)->forceDelete()) {
            session()->flash('message', self::DELETE_MESSAGE);
        } else {
            session()->flash('message', self::ERROR_MESSAGE);
        }
    }

    /**
     * Reset input fields.
     */
    private function resetInputFields(): void
    {
        $this->selectedProduct = null;
        $this->productOptions = collect();
        $this->selectedProductOption = null;
    }

    /**
     * Validation rules.
     *
     * @return array
     */
    protected function rules(): array
    {
        return [
            'selectedProduct' => ['required', 'exists:products,id'],
            'selectedProductOption' => ['required', 'exists:product_options,id'],
        ];
    }

    /**
     * Create or update a collection product.
     */
    private function createOrUpdateCollectionProduct(): void
    {
        CollectionProduct::updateOrCreate(
            [
                'collection_id' => $this->collectionId,
                'product_id' => $this->selectedProduct,
            ],
            [
                'product_option_id' => $this->selectedProductOption,
            ]
        );
    }

    /**
     * Store new collection products.
     */
    public function store(): void
    {
        $this->validate($this->rules());
        $this->createOrUpdateCollectionProduct();
        $this->resetInputFields();
        session()->flash('message', self::SUCCESS_MESSAGE);
    }

    /**
     * Render the component.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        $this->cProducts = CollectionProduct::where(
                'collection_id', $this->collectionId
            )->with('product')->get();
        return view('prody::livewire.collection-products');
    }
}
