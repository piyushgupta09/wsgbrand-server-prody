<?php

namespace Fpaipl\Prody\Datatables;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Fpaipl\Prody\Models\Brand as Model;
use Fpaipl\Panel\Datatables\ModelDatatable;

class BrandDatatable extends ModelDatatable
{
    const SORT_SELECT_DEFAULT = 'updated_at#desc';
       
    public static function baseQuery($model): Builder
    {
        return  $model::query();
    }

    public function selectOptions($field): Collection
    {
        switch ($field) {
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
                    'route' => 'brands.create',
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
                'route' => 'brands.show',
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
                        'placeholder' => 'Name',
                        'component' => 'forms.input-box',
                        'attributes' => ['required'],
                        'rows' => ''
                    ],
    
    
                ],
            ),
            parent::getDefaultImageColumn(),
            parent::getDefaultSlugColumns(),
            parent::getDefaultPostColumns(),
        );
    }

}