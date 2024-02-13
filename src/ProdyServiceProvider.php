<?php

namespace Fpaipl\Prody;

use Livewire\Livewire;
use Illuminate\Support\ServiceProvider;
use Fpaipl\Prody\Http\Livewire\ProductStock;
use Fpaipl\Prody\Http\Livewire\ProductRanges;
use Fpaipl\Prody\Http\Livewire\ProductStatus;
use Fpaipl\Prody\Http\Livewire\MaterialRanges;
use Fpaipl\Prody\Http\Livewire\ProductDetails;
use Fpaipl\Prody\Http\Livewire\ProductOptions;
use Fpaipl\Prody\Http\Livewire\ProductPricing;
use Fpaipl\Prody\Http\Livewire\MaterialOptions;
use Fpaipl\Prody\Http\Livewire\ProductSections;
use Fpaipl\Prody\Http\Livewire\ProductAttribute;
use Fpaipl\Prody\Http\Livewire\ProductCostsheet;
use Fpaipl\Prody\Http\Livewire\ProductDecisions;
use Fpaipl\Prody\Http\Livewire\ProductMaterials;
use Fpaipl\Prody\Http\Livewire\ProductOverheads;
use Fpaipl\Prody\Http\Livewire\ProductProcesses;
use Fpaipl\Prody\Http\Livewire\ProductAttributes;
use Fpaipl\Prody\Http\Livewire\ProductFixedcosts;
use Fpaipl\Prody\Http\Livewire\CollectionProducts;
use Fpaipl\Prody\Http\Livewire\ProductConsumables;
use Fpaipl\Prody\Http\Livewire\ProductPartyLedger;
use Fpaipl\Prody\Http\Livewire\ProductMeasurements;
use Fpaipl\Prody\Http\Livewire\ProductRangesMaterial;
use Fpaipl\Prody\Http\Livewire\ProductOptionsMaterial;
use Fpaipl\Prody\Http\Livewire\ProductCostsheetPreview;

class ProdyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->loadViewsFrom(__DIR__.'/resources/views', 'prody');
        $this->loadViewComponentsAs('prody', []);

        Livewire::component('material-options', MaterialOptions::class);
        Livewire::component('material-ranges', MaterialRanges::class);
        Livewire::component('product-materials', ProductMaterials::class);
        Livewire::component('product-decisions', ProductDecisions::class);
        Livewire::component('product-details', ProductDetails::class);
        Livewire::component('product-stock', ProductStock::class);

        Livewire::component('product-attributes', ProductAttributes::class);
        Livewire::component('product-measurements', ProductMeasurements::class);
        Livewire::component('product-sections', ProductSections::class);
        Livewire::component('product-costsheet', ProductCostsheet::class);
        Livewire::component('product-fixedcosts', ProductFixedcosts::class);
        Livewire::component('product-consumables', ProductConsumables::class);
        Livewire::component('product-party-ledger', ProductPartyLedger::class);
        Livewire::component('product-pricing', ProductPricing::class);
        Livewire::component('product-options-material', ProductOptionsMaterial::class);
        Livewire::component('product-options', ProductOptions::class);
        Livewire::component('product-ranges-material', ProductRangesMaterial::class);
        Livewire::component('product-ranges', ProductRanges::class);
        Livewire::component('product-overheads', ProductOverheads::class);
        Livewire::component('product-processes', ProductProcesses::class);
        Livewire::component('product-status', ProductStatus::class);
        Livewire::component('collection-products', CollectionProducts::class);
        Livewire::component('product-costsheet-preview', ProductCostsheetPreview::class);
    }
}
