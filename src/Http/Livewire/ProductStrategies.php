<?php

namespace Fpaipl\Prody\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Str;
use Fpaipl\Prody\Models\Product;
use Fpaipl\Prody\Models\Discount;
use Fpaipl\Prody\Models\Strategy;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Fpaipl\Prody\Models\RefundPolicy;
use Fpaipl\Prody\Models\ReturnPolicy;
use Illuminate\Support\Facades\Cache;
use Fpaipl\Prody\Models\ProductDiscount;
use Fpaipl\Prody\Models\ProductStrategy;
use Fpaipl\Prody\Models\ProductRefundPolicy;
use Fpaipl\Prody\Models\ProductReturnPolicy;

/**
 * Handles the creation and update of product strategies including
 * strategy, discount, refund policy, and return policy decisions.
 */
class ProductStrategies extends Component
{
    public $decisionGroups;
    public $productDecision;
    public $decisions;
    public $product;
    public $selected = [];
    public $routeValue;

    /**
     * Initialize component with product and decision data.
     *
     * @param  int|string $modelId The ID of the product.
     * @param  array $decisions Array of decisions.
     */
    public function mount($modelId, $decisions)
    {
        $this->product = Product::with('productDecisions')->find($modelId);
        $this->decisions = $decisions;
        $this->decisionGroups = $this->getDecisionGroups();
        $this->routeValue = [
            'product' => $this->product->slug, 
            'section' => 'strategy',
            'tab' => 'decisions',
        ];
        $this->reloadData();
    }

    /**
     * Reload and initialize the component data with saved or default decision values.
     */
    public function reloadData()
    {
        $defaults = config('prody.decision_values');

        foreach ($this->decisions as $decision) {
            $decisionType = $decision['type'];
            foreach ($this->decisionGroups as $group) {
                $slug = $group['slug'];
                $fieldName = Str::snake($slug) . '_id';
                
                $modelClass = 'Fpaipl\\Prody\\Models\\Product' . Str::studly($slug);
                $savedDecision = $modelClass::where('product_id', $this->product->id)
                                            ->where('decision', $decisionType)
                                            ->first();
                                            
                $this->selected[$decisionType][$slug] = $savedDecision->{$fieldName} ?? $defaults[$decisionType][$slug] ?? null;
            }
        }
    }

    /**
     * Resolves the model instance based on decision type and group slug.
     *
     * @param  string $decisionType The type of decision being made.
     * @param  string $groupSlug The slug of the decision group.
     * @return mixed|null Model instance or null if not found.
     */
    private function getModel($decisionType, $groupSlug)
    {
        if (!isset($this->selected[$decisionType][$groupSlug])) {
            return null;
        }
        $class = 'Fpaipl\\Prody\\Models\\Product' . Str::studly(Str::replace('-', '', $groupSlug));
        $modelId = $this->selected[$decisionType][$groupSlug];
        return $class::find($modelId);
    }

    /**
     * Saves the selected decisions for the product.
     *
     * @param  string $decisionType The type of decision being saved.
     */
    public function saveDecision($decisionType)
    {
        DB::beginTransaction();

        dd($this->decisionGroups, $decisionType, $this->product->id, $this->selected[$decisionType]);
        
        try {
            foreach ($this->decisionGroups as $group) {
                $model = $this->getModel($decisionType, $group['slug']);
                Log::info([$decisionType, $group['slug']]);
                if ($model) {
                    switch ($group['slug']) {
                        case 'strategy':
                            ProductStrategy::updateOrCreate(
                                [
                                    'decision' => $decisionType,
                                    'product_id' => $this->product->id,
                                ],
                                [
                                    'strategy_id' => $model->id,
                                    'name' => $model->name,
                                    'math' => $model->math,
                                    'value' => $model->value,
                                    'type' => $model->type,
                                    'details' => $model->details,
                                ]
                            );
                            break;
    
                        case 'discount':
                            ProductDiscount::updateOrCreate(
                                [
                                    'decision' => $decisionType,
                                    'product_id' => $this->product->id,
                                ],
                                [
                                    'discount_id' => $model->id,
                                    'value' => $model->value,
                                    'type' => $model->type,
                                    'details' => $model->details,
                                    'one_time' => $model->one_time,
                                    'multi_time' => $model->multi_time,
                                    'on_quantity' => $model->on_quantity,
                                    'on_total' => $model->on_total,
                                    'on_account' => $model->on_account,
                                    'on_checkout' => $model->on_checkout,
                                    'on_product' => $model->on_product,
                                    'min_quantity' => $model->min_quantity,
                                    'max_quantity' => $model->max_quantity,
                                    'min_total' => $model->min_total,
                                    'max_total' => $model->max_total,
                                ]
                            );
                            break;
                
                        case 'refund-policy':
                            ProductRefundPolicy::updateOrCreate(
                                [
                                    'decision' => $decisionType,
                                    'product_id' => $this->product->id,
                                ],
                                [
                                    'refund_policy_id' => $model->id,
                                ]
                            );
                            break;
    
                        case 'return-policy':
                            ProductReturnPolicy::updateOrCreate(
                                [
                                    'decision' => $decisionType,
                                    'product_id' => $this->product->id,
                                ],
                                [
                                    'return_policy_id' => $model->id,
                                ]
                            );
                            break;
                    
                        default: break;
                    }
                } else {
                    throw new \Exception('Model not found');
                }
            }
            DB::commit();
            session()->flash('success', 'Product strategy saved successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            session()->flash('error', 'Failed to save product strategy.');
        }
    }

    /**
     * Retrieve and cache the decision groups and their items.
     *
     * @return array Array of decision groups with their items.
     */
    private function getDecisionGroups()
    {
        return Cache::remember('decision_groups', 3600, function () {
            return [
                ['name' => 'Strategy', 'slug' => 'strategy', 'items' => Strategy::all()],
                ['name' => 'Discount', 'slug' => 'discount', 'items' => Discount::all()],
                ['name' => 'Refund Policy', 'slug' => 'refund-policy', 'items' => RefundPolicy::all()],
                ['name' => 'Return Policy', 'slug' => 'return-policy', 'items' => ReturnPolicy::all()],
            ];
        });
    }

    /**
     * Renders the Livewire component view.
     *
     * @return \Illuminate\View\View Livewire view.
     */
    public function render()
    {
        return view('prody::livewire.product-strategies');
    }
}