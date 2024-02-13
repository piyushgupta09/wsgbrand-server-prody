<?php
  
namespace Fpaipl\Prody\Http\Livewire;
  
use Livewire\Component;
use Fpaipl\Prody\Models\Product;
  
class ProductDecisions extends Component
{
    public $buyDecisions;
    public $sellDecisions;
    public $decisionLocked;
    public $modelId;
    public $model;

    public function mount($modelId)
    {
        $this->modelId = $modelId;
        $this->model = Product::find($modelId);
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->reloadData();
    }
    
    public function reloadData()
    {
        $this->decisionLocked = $this->model->decision_locked;
        $this->buyDecisions = collect([
            [
                'name' => 'vendor',
                'image' => 'https://icon-library.com/images/e-commerce-icon-png/e-commerce-icon-png-5.jpg',
                'label' => 'Buy from vendor (as per customer order)',
                'value' => $this->model->vendor,
            ],
            [
                'name' => 'factory',
                'image' => 'https://icon-library.com/images/e-commerce-icon-png/e-commerce-icon-png-5.jpg',
                'label' => 'Buy from factory (as per self order)',
                'value' => $this->model->factory
            ],
            [
                'name' => 'market',
                'image' => 'https://icon-library.com/images/e-commerce-icon-png/e-commerce-icon-png-5.jpg',
                'label' => 'Buy from market (as per self decision)',
                'value' => $this->model->market,
            ]
        ]);
        $this->sellDecisions = collect([
            [
                'name' => 'ecomm',
                'image' => 'https://icon-library.com/images/e-commerce-icon-png/e-commerce-icon-png-5.jpg',
                'label' => 'Sell online ecomm store',
                'value' => $this->model->ecomm,
            ],
            [
                'name' => 'retail',
                'image' => 'https://icon-library.com/images/e-commerce-icon-png/e-commerce-icon-png-5.jpg',
                'label' => 'Sell on retailpur',
                'value' => $this->model->retail,
                'url' => 'https://retailpur.com',
            ],
            [
                'name' => 'inbulk',
                'image' => 'https://icon-library.com/images/e-commerce-icon-png/e-commerce-icon-png-5.jpg',
                'label' => 'Sell on wholesaleGuruji',
                'value' => $this->model->inbulk,
            ],
            [
                'name' => 'offline',
                'image' => 'https://icon-library.com/images/e-commerce-icon-png/e-commerce-icon-png-5.jpg',
                'label' => 'Sell offline dukaan',
                'value' => $this->model->offline,
            ],
        ]);
    }

    public function updateDecision($decisionName)
    {
        // If the decision is locked, don't allow any changes
        if ($this->decisionLocked) {
            return redirect()->route('products.show', $this->model->slug)->with([
                'toast' => [
                    'class' => 'danger',
                    'text' => 'Product decision is locked. Please unlock to make changes.',
                ]
            ]);
        }

        // from factory, vendor or market only one can be true
        if ($decisionName == 'factory' || $decisionName == 'vendor' || $decisionName == 'market') {
            // First, reset the other fields if the current field is being activated
            if (!$this->model->$decisionName) {
                $this->model->vendor = false;
                $this->model->factory = false;
                $this->model->market = false;
            }
        }

        // Then, toggle the decision value
        $this->model->$decisionName = !$this->model->$decisionName;

        // Save the updated model
        $this->model->save();

        // Reload the data to reflect changes
        $this->reloadData();

        // Redirect to the product show page
        return redirect()->route('products.show', $this->model->slug)->with([
            'toast' => [
                'class' => 'success',
                'text' => 'Product decision updated successfully.',
            ]
        ]);
    }

    public function loackDecision()
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

    // private function checkDecisionPending()
    // {
    //     $this->decisionPending = true;
    //     // first merge the buy and sell decisions
    //     $decisions = $this->buyDecisions->merge($this->sellDecisions);
    //     // now loop thru the decisions and check if any decision is true then set decisionPending to false and exit
    //     foreach ($decisions as $decision) {
    //         if ($decision['value']) {
    //             $this->decisionPending = false;
    //             break;
    //         }
    //     }
    // }

    public function render()
    {
        return view('prody::livewire.product-decisions');
    }
}
