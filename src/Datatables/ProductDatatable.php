<?php

namespace Fpaipl\Prody\Datatables;

use Fpaipl\Prody\Models\Brand;
use Fpaipl\Prody\Models\Category;
use Fpaipl\Prody\Models\Tax;
use Fpaipl\Prody\Models\Product as Model;
use Illuminate\Database\Eloquent\Builder;
use Fpaipl\Panel\Datatables\ModelDatatable;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;

class ProductDatatable extends ModelDatatable
{
    const SORT_SELECT_DEFAULT = 'updated_at#desc';
    
    public static function baseQuery($model): Builder
    {
        return  $model::query();
    }

    public function selectOptions($field): EloquentCollection
    {
        switch ($field) {
            case 'brand_id': 
                return Brand::all();

            case 'category_id': 
                return new EloquentCollection(Category::canHaveChildren()->get()->map(function ($category) {
                    return [
                        'id' => $category->id,
                        'name' => $category->getFullName($category),
                    ];
                })->sortBy('name'));

            case 'tax_id': 
                return Tax::all();

            case 'status': 
                return new EloquentCollection(collect(Model::STATUS)->all());

            default: 
                return new EloquentCollection();
        }
    }

    public function topButtons(): array
    {
        return array_merge(
            array(
                'add_new' => [
                    'show' => [
                        'active' => true,
                        'trash' => false,
                    ],
                    'label' => 'Create',
                    'icon' => 'bi bi-plus-lg',
                    'type' => 'buttons.action-link',
                    'style' => '',
                    'route' => 'products.create',
                    'function' => ''
                ],
            ),
        );
    }

    public function tableButtons(): array
    {
        return array(
            'view' => [
                'show' => [
                    'active' => $this->features()['row_actions']['show']['view']['active'],
                    'trash' => $this->features()['row_actions']['show']['view']['trash'],
                ],
                'label' => 'View',
                'icon' => 'bi bi-chevron-right',
                'type' => 'buttons.action-link',
                'style' => '',
                'route' => 'products.show',
                'function' => '',
                'confirm' => false,
            ],
        );
    }

    public function getColumns(): array
    {
        return array_merge(
            parent::getDefaultPreColumns(),
            array(
                'brand_id' => [
                    'name' => 'brand_id',
                    'labels' => [
                        'table' => 'Brand',
                        'export' => 'Brand Name'
                    ],
                    'thead' => [
                        'view' => 'buttons.sortit',
                        'value' => '',
                        'align' => '',
                    ],
                    'tbody' => [
                        'view' => 'cells.text-value',
                        'value' => 'getBrandName',
                        'align' => '',
                    ],
                    'viewable' => [
                        'active' => false,
                        'trash' => false
                    ],
                    'expandable' => [
                        'active' => true,
                        'trash' => false
                    ],
                    'sortable' => false,
                    'filterable' => [
                        'active' => true,
                        'trash' => false
                    ],
                    'importable' => true,
                    'exportable' => [
                        'active' => true,
                        'trash' => true,
                        'value' => 'getBrandName'
                    ],
                    'artificial' => false,
                    'fillable' => [
                        'type' => '',
                        'style' => '',
                        'p_style' => 'col-6',
                        'placeholder' => 'Choose Brand Name',
                        'component' => 'forms.select-option',
                        'options' =>  [
                            'data' => self::selectOptions('brand_id'),
                            'withRelation' => true,
                            'relation' => '',
                            'default' => '1',
                        ],
                        'attributes' => ['required'],
                        'rows' => ''
                    ],
                ],
                'category_id' => [
                    'name' => 'category_id',
                    'labels' => [
                        'table' => 'Category',
                        'export' => 'Category Name'
                    ],
                    'thead' => [
                        'view' => 'buttons.sortit',
                        'value' => '',
                        'align' => '',
                    ],
                    'tbody' => [
                        'view' => 'cells.text-value',
                        'value' => 'getTableData',
                        'align' => '',
                    ],
                    'viewable' => [
                        'active' => true,
                        'trash' => false
                    ],
                    'expandable' => [
                        'active' => true,
                        'trash' => false
                    ],
                    'sortable' => false,
                    'filterable' => [
                        'active' => true,
                        'trash' => false
                    ],
                    'importable' => true,
                    'exportable' => [
                        'active' => true,
                        'trash' => true,
                        'value' => 'getTableData'
                    ],
                    'artificial' => false,
                    'fillable' => [
                        'type' => '',
                        'p_style' => 'col-6',
                        'style' => '',
                        'placeholder' => 'Choose Category',
                        'component' => 'forms.select-option',
                        'options' =>  [
                            'data' => self::selectOptions('category_id'),
                            'withRelation' => true,
                            'relation' => 'child',
                        ],
                        'attributes' => ['required','autofocus'],
                        'rows' => ''
                    ],
                   
    
                ],
                
                'moq' => [
                    'name' => 'moq',
                    'labels' => [
                        'table' => 'MOQ (Color Set)',
                        'export' => 'MOQ'
                    ],
                    'thead' => [
                        'view' => 'buttons.sortit',
                        'value' => '',
                        'align' => '',
                    ],
                    'tbody' => [
                        'view' => 'cells.text-value',
                        'value' => '',
                        'align' => '',
                    ],
                    'viewable' => [
                        'active' => false,
                        'trash' => false
                    ],
                    'expandable' => [
                        'active' => true,
                        'trash' => true
                    ],
                    'sortable' => true,
                    'filterable' => [
                        'active' => true,
                        'trash' => true
                    ],
                    'importable' => true,
                    'exportable' => [
                        'active' => true,
                        'trash' => true,
                        'value' => 'getValue'
                    ],
                    'artificial' => false,
                    'fillable' => [
                        'type' => 'text',
                        'style' => '',
                        'p_style' => 'col-6',
                        'placeholder' => 'MOQ',
                        'component' => 'forms.input-box',
                        'attributes' => ['required'],
                        'rows' => '',
                        'default' => '1'
                    ],
                    
    
                ],
                'tax_id' => [
                    'name' => 'tax_id',
                    'labels' => [
                        'table' => 'HSN Code (Tax Rate)',
                        'export' => 'Tax Name'
                    ],
                    'thead' => [
                        'view' => 'buttons.sortit',
                        'value' => '',
                        'align' => '',
                    ],
                    'tbody' => [
                        'view' => 'cells.text-value',
                        'value' => 'getTableData',
                        'align' => '',
                    ],
                    'viewable' => [
                        'active' => false,
                        'trash' => false
                    ],
                    'expandable' => [
                        'active' => true,
                        'trash' => false
                    ],
                    'sortable' => false,
                    'filterable' => [
                        'active' => true,
                        'trash' => false
                    ],
                    'importable' => true,
                    'exportable' => [
                        'active' => true,
                        'trash' => true,
                        'value' => 'getTableData'
                    ],
                    'artificial' => false,
                    'fillable' => [
                        'type' => '',
                        'style' => '',
                        'p_style' => 'col-6',
                        'placeholder' => 'Choose Tax',
                        'component' => 'forms.select-option',
                        'options' =>  [
                            'data' => self::selectOptions('tax_id'),
                            'withRelation' => true,
                            'relation' => '',
                        ],
                        'attributes' => ['required','autofocus'],
                        'rows' => ''
                    ],
                ],

                'name' => [
                    'name' => 'name',
                    'labels' => [
                        'table' => 'Name',
                        'export' => 'Name'
                    ],
    
                    'thead' => [
                        'view' => 'buttons.sortit',
                        'value' => '',
                        'align' => '',
                    ],
                    'tbody' => [
                        'view' => 'cells.text-value',
                        'value' => '',
                        'align' => '',
                    ],
                    'viewable' => [
                        'active' => true,
                        'trash' => true
                    ],
                    'expandable' => [
                        'active' => false,
                        'trash' => false
                    ],
                    'sortable' => true,
                    'filterable' => [
                        'active' => true,
                        'trash' => true
                    ],
                    'importable' => true,
                    'exportable' => [
                        'active' => true,
                        'trash' => true,
                        'value' => 'getValue'
                    ],
                    'artificial' => false,
                    'fillable' => [
                        'type' => 'text',
                        'p_style' => 'col-6',
                        'style' => '',
                        'placeholder' => 'Name',
                        'component' => 'forms.input-box',
                        'attributes' => ['required'],
                        'rows' => ''
                    ],
                    
    
                ],
                'code' => [
                    'name' => 'code',
                    'labels' => [
                        'table' => 'Style Id',
                        'export' => 'Style Id'
                    ],
    
                    'thead' => [
                        'view' => 'buttons.sortit',
                        'value' => '',
                        'align' => '',
                    ],
                    'tbody' => [
                        'view' => 'cells.text-value',
                        'value' => '',
                        'align' => '',
                    ],
                    'viewable' => [
                        'active' => true,
                        'trash' => false
                    ],
                    'expandable' => [
                        'active' => true,
                        'trash' => true
                    ],
                    'sortable' => true,
                    'filterable' => [
                        'active' => true,
                        'trash' => true
                    ],
                    'importable' => true,
                    'exportable' => [
                        'active' => true,
                        'trash' => true,
                        'value' => 'getValue'
                    ],
                    'artificial' => false,
                    'fillable' => [
                        'type' => 'text',
                        'style' => '',
                        'p_style' => 'col-6',
                        'placeholder' => 'Style Id',
                        'component' => 'forms.input-box',
                        'attributes' => [],
                        'rows' => ''
                    ],
                    
    
                ],

                'status' => [
                    'name' => 'status',
                    'labels' => [
                        'table' => 'Status',
                        'export' => 'Status'
                    ],
    
                    'thead' => [
                        'view' => 'buttons.sortit',
                        'value' => '',
                        'align' => '',
                    ],
                    'tbody' => [
                        'view' => 'cells.text-value',
                        'value' => 'getTableData',
                        'align' => '',
                    ],
                    'viewable' => [
                        'active' => true,
                        'trash' => true
                    ],
                    'expandable' => [
                        'active' => true,
                        'trash' => true
                    ],
                    'sortable' => true,
                    'filterable' => [
                        'active' => true,
                        'trash' => true
                    ],
                    'importable' => true,
                    'exportable' => [
                        'active' => true,
                        'trash' => true,
                        'value' => 'getTableData'
                    ],
                    'artificial' => true,
                    'fillable' => [],
                ],
            ),
            parent::getDefaultSlugColumns(),
            parent::getDefaultPostColumns(),
        );
    }

}