<?php

namespace Fpaipl\Prody\Http\Controllers;

use Fpaipl\Prody\Actions\LoadTaxes;
use Fpaipl\Prody\Actions\LoadUnits;
use App\Http\Controllers\Controller;
use Fpaipl\Prody\Actions\LoadBrands;
use Fpaipl\Prody\Actions\LoadMaterials;
use Fpaipl\Prody\Actions\LoadCategories;
use Fpaipl\Prody\Actions\LoadCollections;

class SyncController extends Controller
{
    // Monaal 

    public function loadUnits()
    {
        $count = LoadUnits::execute(true);
        $message = $count > 0 ? $count . ' units synced' : 'No new units found';
        return redirect()->route('units.index')->with('toast', [
            'class' => 'success',
            'text' => $message
        ]);
    }

    public function loadMaterials()
    {
        $count = LoadMaterials::execute(config('monaal.url'), config('monaal.supplier_id'), true);
        $message = $count > 0 ? $count . ' materials synced' : 'No new materials found';
        return redirect()->route('materials.index')->with('toast', [
            'class' => 'success',
            'text' => $message
        ]);
    }

    // WSG

    public function loadTaxes()
    {
        $count = LoadTaxes::execute(true);
        $message = $count > 0 ? $count . ' taxes synced' : 'No new taxes found';
        return redirect()->route('taxes.index')->with('toast', [
            'class' => 'success',
            'text' => $message
        ]);
    }

    public function loadBrands()
    {
        $count = LoadBrands::execute(true);
        $message = $count > 0 ? $count . ' brands synced' : 'No new brands found';
        return redirect()->route('brands.index')->with('toast', [
            'class' => 'success',
            'text' => $message
        ]);
    }

    public function loadCategories()
    {
        $count = LoadCategories::execute(true);
        $message = $count > 0 ? $count . ' categories synced' : 'No new categories found';
        return redirect()->route('categories.index')->with('toast', [
            'class' => 'success',
            'text' => $message
        ]);
    }

    public function loadCollections()
    {
        $count = LoadCollections::execute(true);
        $message = $count > 0 ? $count . ' collections synced' : 'No new collections found';
        return redirect()->route('collections.index')->with('toast', [
            'class' => 'success',
            'text' => $message
        ]);
    }
}
