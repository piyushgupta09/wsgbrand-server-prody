<?php

namespace Fpaipl\Prody\Datatables;

use Fpaipl\Prody\Models\Unit;
use Fpaipl\Prody\Models\Supplier;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;
use Fpaipl\Prody\Models\Material as Model;
use Fpaipl\Panel\Datatables\ModelDatatable;

class MaterialDatatable extends ModelDatatable
{
    const SORT_SELECT_DEFAULT = 'updated_at#desc';
    
    const DUPLICATE = false;

    public static function baseQuery($model): Builder
    {
        return  $model::query();
    }

    public function selectOptions($field): Collection
    {
        switch ($field) {
            case 'category_type': 
                return new Collection(collect(config('prody.fabric_category_types'))->map(function ($item, $key) {
                    return [
                        'id' => $key,
                        'name' => $item
                    ];
                }));
            case 'unit_name': 
                return new Collection(collect(config('prody.fabric_units'))->map(function ($item, $key) {
                    return [
                        'id' => $key,
                        'name' => $item
                    ];
                }));
            case 'supplier_id': return Collection::make(Supplier::all()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->name
                ];
            }));
            default: return collect();
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
                    'icon' => 'bi bi-plus-lg',
                    'label' => 'Create',
                    'type' => 'buttons.action-link',
                    'style' => '',
                    'route' => 'materials.create',
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
                'route' => 'materials.show',
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
                'sid' => [
                    'name' => 'sid',
                    'labels' => [
                        'table' => 'SID',
                        'export' => 'SID'
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
                        'style' => '',
                        'p_style' => 'col-6',
                        'placeholder' => 'SID',
                        'component' => 'forms.input-box',
                        'attributes' => ['required'],
                        'rows' => ''
                    ],
                ],
                'supplier_id' => [
                    'name' => 'supplier_id',
                    'labels' => [
                        'table' => 'Supplier Name',
                        'export' => 'Supplier Name',
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
                        'p_style' => 'col-md-6',
                        'placeholder' => 'Choose Supplier',
                        'component' => 'forms.select-option',
                        'options' =>  [
                            'data' => self::selectOptions('supplier_id'),
                            'withRelation' => false,
                            'relation' => '',
                        ],
                        'attributes' => ['required'],
                        'rows' => ''
                    ],
                ],
              
                'category' => [
                    'name' => 'category_name',
                    'labels' => [
                        'table' => 'Category Name',
                        'export' => 'Category Name'
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
                        'style' => '',
                        'p_style' => 'col-md-4',
                        'placeholder' => 'Category Name',
                        'component' => 'forms.input-box',
                        'attributes' => ['required'],
                        'rows' => ''
                    ],
                ],
                'type' => [
                    'name' => 'category_type',
                    'labels' => [
                        'table' => 'Category Type',
                        'export' => 'Category Type'
                    ],
    
                    'thead' => [
                        'view' => 'buttons.sortit',
                        'value' => '',
                        'align' => '',
                    ],
                    'tbody' => [
                        'view' => 'cells.select-value',
                        'value' => '',
                        'align' => '',
                    ],
                    'viewable' => [
                        'active' => false,
                        'trash' => false
                    ],
                    'expandable' => [
                        'active' => false,
                        'trash' => false
                    ],
                    'sortable' => false,
                    'filterable' => [
                        'active' => false,
                        'trash' => false
                    ],
                    'importable' => false,
                    'exportable' => [
                        'active' => false,
                        'trash' => false,
                        'value' => 'getValue'
                    ],
                    'artificial' => false,
                    'fillable' => [
                        'type' => '',
                        'style' => '',
                        'p_style' => 'col-md-4',
                        'placeholder' => 'Choose Category Type',
                        'component' => 'forms.select-option',
                        'options' =>  [
                            'data' => self::selectOptions('category_type'),
                            'withRelation' => false,
                            'relation' => '',
                        ],
                        'attributes' => ['required'],
                        'rows' => ''
                    ],
                ],

                'unit_name' => [
                    'name' => 'unit_name',
                    'labels' => [
                        'table' => 'Unit',
                        'export' => 'Unit Name'
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
                        'p_style' => 'col-md-4',
                        'placeholder' => 'Choose Unit',
                        'component' => 'forms.select-option',
                        'options' =>  [
                            'data' => self::selectOptions('unit_name'),
                            'withRelation' => true,
                            'relation' => '',
                        ],
                        'attributes' => ['required'],
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
                        'value' => 'getTableData',
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
                        'value' => 'getTableData'
                    ],
                    'artificial' => false,
                    'fillable' => [
                        'type' => 'text',
                        'style' => '',
                        'p_style' => 'col-6',
                        'placeholder' => 'Name',
                        'component' => 'forms.input-box',
                        'attributes' => ['required'],
                        'rows' => ''
                    ],
                    
    
                ],

                'price' => [
                    'name' => 'price',
                    'labels' => [
                        'table' => 'Buy Rate',
                        'export' => 'Buy Rate',
                    ],
    
                    'thead' => [
                        'view' => 'buttons.sortit',
                        'value' => '',
                        'align' => 'text-end',
                    ],
                    'tbody' => [
                        'view' => 'cells.text-value',
                        'value' => 'getTableData',
                        'align' => 'text-end',
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
                        'value' => 'getTableData'
                    ],
                    'artificial' => false,
                    'fillable' => [
                        'type' => 'text',
                        'style' => '',
                        'p_style' => 'col-6',
                        'placeholder' => 'Buy Rate',
                        'component' => 'forms.input-box',
                        'attributes' => [],
                        'rows' => '',
                        'note' => 'Exclusive of Tax'
                    ],
                    'showable' => true,
                ],

                'details' => [
                    'name' => 'details',
                    'labels' => [
                        'table' => 'Details',
                        'export' => 'Details'
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
                        'active' => false,
                        'trash' => false
                    ],
                    'sortable' => false,
                    'filterable' => [
                        'active' => false,
                        'trash' => false
                    ],
                    'importable' => false,
                    'exportable' => [
                        'active' => false,
                        'trash' => false,
                        'value' => 'getTableData'
                    ],
                    'artificial' => false,
                    'fillable' => [
                        'type' => 'textarea',
                        'style' => '',
                        'placeholder' => 'Details',
                        'component' => 'forms.textarea-box',
                        'attributes' => [],
                        'rows' => ''
                    ],
                    
    
                ],
                'tags' => [
                    'name' => 'tags',
                    'labels' => [
                        'table' => 'Tags',
                        'export' => 'Tags'
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
                        'active' => false,
                        'trash' => false
                    ],
                    'sortable' => false,
                    'filterable' => [
                        'active' => false,
                        'trash' => false
                    ],
                    'importable' => false,
                    'exportable' => [
                        'active' => false,
                        'trash' => false,
                        'value' => 'getTableData'
                    ],
                    'artificial' => true,
                    'fillable' => [],
                    'showable' => false,
                ],
            ),
            parent::getDefaultPostColumns(),
        );
    }
}