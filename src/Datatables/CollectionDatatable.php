<?php

namespace Fpaipl\Prody\Datatables;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Fpaipl\Prody\Models\Collection as Model;
use Fpaipl\Panel\Datatables\ModelDatatable;

class CollectionDatatable extends ModelDatatable
{
    const SORT_SELECT_DEFAULT = 'updated_at#desc';
       
    public static function baseQuery($model): Builder
    {
        return  $model::query();
    }

    public function selectOptions($field): Collection
    {
        switch ($field) {
            case 'state': 
                return new Collection(['live', 'draft']);

            case 'type' : 
                $collection = collect([
                    [
                        'id' => 'ranged',
                        'name' => 'Ranged',
                    ],
                    [
                        'id' => 'featured',
                        'name' => 'Featured',
                    ],
                    [
                        'id' => 'recommended',
                        'name' => 'Recommended',
                    ],
                ]);
            
                return new Collection($collection->all());

            default: return new Collection(collect());
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
                    'route' => 'collections.create',
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
                'route' => 'collections.show',
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
                'type' => [
                    'name' => 'type',
                    'labels' => [
                        'table' => 'Collection Type',
                        'export' => 'Type'
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
                        'trash' => false,
    
                    ],
                    'sortable' => false,
                    'filterable' => [
                        'active' => true,
                        'trash' => false
                    ],
                    'importable' => true,
                    'exportable' => [
                        'active' => true,
                        'trash' => false,
                        'value' => ''
                    ],
                    'artificial' => false,
                    'fillable' => [
                        'type' => '',
                        'style' => '',
                        'p_style' => 'col-6',
                        'placeholder' => 'Choose Collection Type',
                        'component' => 'forms.select-option',
                        'options' =>  [
                            'data' => self::selectOptions('type'),
                            'withRelation' => false,
                            'relation' => '',
                        ],
                        'attributes' => ['required'],
                        'rows' => ''
                    ],
    
                ],
                'active' => [
                    'name' => 'active',
                    'labels' => [
                        'table' => 'Active State',
                        'export' => 'Active State'
                    ],
                    'thead' => [
                        'view' => 'buttons.sortit',
                        'value' => '',
                        'align' => '',
                    ],
                    'tbody' => [
                        'view' => 'cells.bool-value',
                        'value' => '',
                        'align' => '',
                    ],
                    'viewable' => [
                        'active' => false,
                        'trash' => false
                    ],
                    'expandable' => [
                        'active' => true,
                        'trash' => false,
    
                    ],
                    'sortable' => false,
                    'filterable' => [
                        'active' => true,
                        'trash' => false
                    ],
                    'importable' => true,
                    'exportable' => [
                        'active' => true,
                        'trash' => false,
                        'value' => ''
                    ],
                    'artificial' => false,
                    'fillable' => [
                        'type' => '',
                        'style' => '',
                        'p_style' => 'col-6',
                        'placeholder' => '',
                        'component' => 'forms.radio-option',
                        'options' =>  [
                            'data' => self::selectOptions('state'),
                            'withRelation' => false,
                            'relation' => '',
                            'default' => 'draft'
                        ],
                        'attributes' => ['required'],
                        'rows' => ''
                    ],
                ],
                'order' => [
                    'name' => 'order',
                    'labels' => [
                        'table' => 'Sequence Order',
                        'export' => 'Order'
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
                'info' => [
                    'name' => 'info',
                    'labels' => [
                        'table' => 'Info',
                        'export' => 'Info'
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
                        'p_style' => 'col-12',
                        'placeholder' => 'Name',
                        'component' => 'forms.input-box',
                        'attributes' => ['required'],
                        'rows' => ''
                    ],
                ],
            ),
            parent::getDefaultImagesColumn(),
            parent::getDefaultSlugColumns(),
            parent::getDefaultPostColumns(),
        );
    }

}