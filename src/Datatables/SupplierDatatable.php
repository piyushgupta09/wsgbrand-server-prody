<?php

namespace Fpaipl\Prody\Datatables;

use Fpaipl\Prody\Models\Supplier;
use Illuminate\Database\Eloquent\Builder;
use Fpaipl\Prody\Models\Supplier as Model;
use Fpaipl\Panel\Datatables\ModelDatatable;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;

class SupplierDatatable extends ModelDatatable
{
    const SORT_SELECT_DEFAULT = 'name#asc';

    public static function baseQuery($model): Builder
    {
        return  $model::query()->where('name', '!=', 'Not Available');
    }

    public function selectOptions($field): EloquentCollection
    {
        switch ($field) {
            case 'type':
                return new EloquentCollection(collect(Supplier::TYPES)->map(function ($item, $key) {
                    return [
                        'id' => $key,
                        'name' => $item
                    ];
                }));
                break;
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
                    'route' => 'suppliers.create',
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
                'route' => 'suppliers.show',
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
                    'sortable' => true,
                    'filterable' => [
                        'active' => true,
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
                        'type' => 'hidden',
                        'style' => '',
                        'p_style' => '',
                        'placeholder' => 'SID',
                        'component' => 'forms.input-hidden',
                        'attributes' => [],
                        'rows' => ''
                    ],
                ],
             
                'type' => [
                    'name' => 'type',
                    'labels' => [
                        'table' => 'Supplier Type',
                        'export' => 'Supplier Type'
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
                        'type' => 'select',
                        'style' => '',
                        'p_style' => 'col-6',
                        'placeholder' => 'Choose Supplier Type',
                        'component' => 'forms.select-option',
                        'options' =>  [
                            'data' => self::selectOptions('type'),
                            'withRelation' => false,
                        ],
                        'attributes' => ['required'],
                        'rows' => ''
                    ],
                ],
                'name' => [
                    'name' => 'name',
                    'labels' => [
                        'table' => 'Business Name',
                        'export' => 'Business Name'
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
                        'placeholder' => 'Name',
                        'component' => 'forms.input-box',
                        'attributes' => ['required'],
                        'rows' => ''
                    ],
                ],

                'address' => [
                    'name' => 'address',
                    'labels' => [
                        'table' => 'Address',
                        'export' => 'Address'
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
                        'p_style' => 'col-12',
                        'placeholder' => 'Address',
                        'component' => 'forms.input-box',
                        'attributes' => ['required'],
                        'rows' => ''
                    ],
                ],

                'contact_person' => [
                    'name' => 'contact_person',
                    'labels' => [
                        'table' => 'Contact Person',
                        'export' => 'Contact Person'
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
                        'placeholder' => 'Contact Person',
                        'component' => 'forms.input-box',
                        'attributes' => ['required'],
                        'rows' => ''
                    ],
                ],
                'contact_number' => [
                    'name' => 'contact_number',
                    'labels' => [
                        'table' => 'Contact Number',
                        'export' => 'Contact Number'
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
                        'placeholder' => 'Contact Number',
                        'component' => 'forms.input-box',
                        'attributes' => ['required'],
                        'rows' => ''
                    ],
                ],

                'email' => [
                    'name' => 'email',
                    'labels' => [
                        'table' => 'Email',
                        'export' => 'Email'
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
                        'p_style' => 'col-6',
                        'placeholder' => 'Email',
                        'component' => 'forms.input-box',
                        'attributes' => [],
                        'rows' => ''
                    ],
                ],
                'website' => [
                    'name' => 'website',
                    'labels' => [
                        'table' => 'Website',
                        'export' => 'Website'
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
                        'p_style' => 'col-6',
                        'placeholder' => 'Website',
                        'component' => 'forms.input-box',
                        'attributes' => [],
                        'rows' => ''
                    ],
                ],
               
                'details' => [
                    'name' => 'details',
                    'labels' => [
                        'table' => 'Other Details',
                        'export' => 'Other Details'
                    ],
    
                    'thead' => [
                        'view' => 'buttons.sortit',
                        'value' => '',
                        'align' => '',
                    ],
                    'tbody' => [
                        'view' => 'cells.textarea-value',
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
                        'value' => 'getValue'
                    ],
                    'artificial' => false,
                    'fillable' => [
                        'type' => 'textarea',
                        'style' => '',
                        'p_style' => 'col-6',
                        'placeholder' => 'Other Details',
                        'component' => 'forms.textarea-box',
                        'attributes' => [],
                        'rows' => ''
                    ],
                ],
            ),
            parent::getDefaultImageColumn(),
            parent::getDefaultPostColumns(),
        );
    }

}