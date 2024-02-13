<?php
  
namespace Fpaipl\Prody\Http\Livewire;
  
use Livewire\Component;
use Fpaipl\Prody\Models\Product;
  
class ProductCostsheetNew extends Component
{
    public $decisionLocked;
    public $modelId;
    public $model;

    public function mount($modelId)
    {
        $this->modelId = $modelId;
        $this->model = Product::find($modelId);
        $this->resetForm();
    }

    public function lockDecision()
    {
        /** @var User $user */
        $user = auth()->user();
        if (!$user->isSuperAdmin() && !$user->isOwnerBrand() && !$user->isManagerBrand()) {
            return redirect()->route('products.show', $this->model->slug)->with('toast', [
                'class' => 'danger',
                'text' => 'You are not authorized to unlock.',
            ]);
        }
        
        $this->model->decision_locked = !$this->model->decision_locked;
        $this->model->save();
        $state = $this->model->decision_locked ? 'locked' : 'unlocked';
        return redirect()->route('products.show', $this->model->slug)->with('toast', [
            'class' => 'success',
            'text' => 'Product decision ' . $state . ' successfully.',
        ]);
    }

    public function render()
    {
        return view('prody::livewire.product-costsheet-new');
    }
}
