<?php
  
namespace Fpaipl\Prody\Http\Livewire;

use Fpaipl\Brandy\Models\Employee;
use Livewire\Component;
use Fpaipl\Brandy\Models\Party;
use Fpaipl\Brandy\Models\Ledger;
use Fpaipl\Prody\Models\Product;
  
class ProductPartyLedger extends Component
{
    public $modelId;
    public $model;
    public $parties;
    public $managers;
    public $ledgers;

    public $ledgerId;
    public $ledgerParty;
    public $ledgerManager;

    public $fee_rate;
    public $order_cap;
    public $min_qty;
    public $max_qty;
    public $fab_rate;
    public $party_id; // selected party
    public $manager_id; // selected manager
    public $notes;
    
    public $showForm;
    public $formType;
    public $routeValue;

    protected $listeners = ['selectedParty'];

    public function selectedParty($partyId)
    {
        $this->party_id = $partyId;
    }

    public function mount($modelId)
    {
        $this->showForm = config('prody.show_add_form');
        $this->modelId = $modelId;
        $this->model = Product::find($modelId);
        $this->routeValue = ['product' => $this->model->slug, 'section' => 'parties'];

        // get those employees who ->user has role for manager-brand
        $this->managers = Employee::whereHas('user', function ($query) {
            $query->whereHas('roles', function ($query) {
                $query->where('name', 'manager-brand');
            });
        })->active()->get();
        if ($this->model->productDecisions->factory) {
            $this->fab_rate = 20;
            $this->parties = Party::where('type', Party::PRODUCT_FATORY)->active()->get();
        } else if ($this->model->productDecisions->vendor) {
            $productRanges = $this->model->productRanges->first();
            if ($productRanges) {
                $this->fab_rate = $productRanges->cost;
            } else {
                $this->fab_rate = 0;
            }
            $this->parties = Party::where('type', Party::PRODUCT_VENDOR)->active()->get();
        } else {
            $this->parties = collect();
        }

        // check if supplier is already paired with product
        $this->resetForm();
    }

    public function toggleForm()
    {
        $this->showForm = !$this->showForm;
    }

    public function resetForm()
    {
        $this->formType = 'create';
        $this->ledgerId = null;
        $this->ledgerParty = null;
        $this->ledgerManager = null;
        $this->min_qty = 100;
        $this->max_qty = 1000;
        $this->fee_rate = 1;
        $this->order_cap = 1000;
        $this->party_id = null;
        $this->manager_id = $this->managers->first()?->id;
        $this->notes = null;
        $this->reloadData();
    }

    public function reloadData()
    {
        $this->ledgers = $this->model->ledgers;
        $this->routeValue = [
            'tab' => request()->tab,
            'product' => $this->model->slug,
            'section' => request()->section,
        ];
    }

    public function store()
    {
        $this->validate([
            'manager_id' => 'required|exists:employees,id',
            'party_id' => 'required|exists:parties,id',
            'min_qty' => 'required|numeric',
            'max_qty' => 'required|numeric',
            'fee_rate' => 'required|numeric',
            'order_cap' => 'required|numeric',
            'fab_rate' => 'required|numeric',
            'notes' => 'nullable|string|max:255',
        ]);

        $partyName = Party::find($this->party_id)->business;

        // check if ledger already exists for the party and product with trash record
        $ledger = Ledger::withTrashed()->where('party_id', $this->party_id)->where('product_id', $this->model->id)->first();

        // check if stock exists for the product
        if (!$this->model->stock) {
            return redirect()->route('products.show', $this->routeValue)->with('toast', [
                'class' => 'danger',
                'text' => 'Stock not found for this product.',
            ]);
        }
        
        if ($ledger) {
            // restore the ledger if it is soft deleted
            if ($ledger->trashed()) {
                $ledger->restore();
            } 
            $ledger->update([
                'fee_rate' => $this->fee_rate,
                'order_cap' => $this->order_cap,
                'min_qty' => $this->min_qty,
                'max_qty' => $this->max_qty,
                'fab_rate' => $this->fab_rate,
                'notes' => $this->notes ?? '',
                'employee_id' => $this->manager_id,
            ]);

            return redirect()->route('products.show', $this->routeValue)->with('toast', [
                'class' => 'success',
                'text' => 'Party Ledger updated successfully.',
            ]);
        }

        $this->model->ledgers()->create([
            'employee_id' => $this->manager_id,
            'party_id' => $this->party_id,
            'fee_rate' => $this->fee_rate,
            'order_cap' => $this->order_cap,
            'min_qty' => $this->min_qty,
            'max_qty' => $this->max_qty,
            'fab_rate' => $this->fab_rate,
            'name' => $this->model->name . "-" . $partyName,
            'product_sid' => $this->model->code,
            'notes' => $this->notes ?? '',
            'stock_id' => $this->model->stock?->id,
        ]);

        return redirect()->route('products.show', $this->routeValue)->with('toast', [
            'class' => 'success',
            'text' => 'Party Ledger created successfully.',
        ]);
    }

    public function edit($ledgerId)
    {
        // Find the product option with associated pomos
        $ledger = Ledger::find($ledgerId);
        $this->ledgerId = $ledgerId;
        $this->ledgerManager = $ledger->manager;
        $this->ledgerParty = $ledger->party;
        $this->min_qty = $ledger->min_qty;
        $this->max_qty = $ledger->max_qty;
        $this->fee_rate = $ledger->fee_rate;
        $this->order_cap = $ledger->order_cap;
        $this->fab_rate = $ledger->fab_rate;
        $this->party_id = $ledger->party_id;
        $this->notes = $ledger->notes;
        $this->formType = 'edit';
        $this->showForm = true;
    }

    public function update()
    {
        $this->validate([
            'fee_rate' => 'required|numeric',
            'order_cap' => 'required|numeric',
            'min_qty' => 'required|numeric',
            'max_qty' => 'required|numeric',
            'fab_rate' => 'required|numeric',
            'notes' => 'nullable|string|max:255',
            'manager_id' => 'required|exists:employees,id',
        ]);

        try {
            $ledger = Ledger::find($this->ledgerId);
            $ledger->update([
                'fee_rate' => $this->fee_rate,
                'order_cap' => $this->order_cap,
                'min_qty' => $this->min_qty,
                'max_qty' => $this->max_qty,
                'fab_rate' => $this->fab_rate,
                'notes' => $this->notes,
                'employee_id' => $this->manager_id,
            ]);
            return redirect()->route('products.show', $this->routeValue)->with('toast', [
                'class' => 'success',
                'text' => 'Party Ledger updated successfully.',
            ]);
        } catch (\Exception $e) {
            return redirect()->route('products.show', $this->routeValue)->with('toast', [
                'class' => 'danger',
                'text' => 'Party Ledger could not be updated.',
            ]);
        }

    }

    public function removeParty()
    {
        $this->ledgerParty = null;
    }

    public function delete($ledgerId)
    {
        $ledger = Ledger::find($ledgerId);
        
        if ($ledger->orders->count()) {
            return redirect()->route('products.show', $this->routeValue)->with('toast', [
                'class' => 'danger',
                'text' => 'Party Ledger cannot be deleted as it has orders.',
            ]);
        }
        
        $ledger->delete();

        return redirect()->route('products.show', $this->routeValue)->with('toast', [
            'class' => 'success',
            'text' => 'Party Ledger deleted successfully.',
        ]);
    }

    public function render()
    {
        return view('prody::livewire.product-party-ledger');
    }
}
