<?php

namespace Fpaipl\Prody\Datatables;

use Fpaipl\Prody\Models\Category as Model;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;
use Fpaipl\Panel\Datatables\ModelDatatable;

class CategoryDatatable extends ModelDatatable
{
    const SORT_SELECT_DEFAULT = 'updated_at#desc';
 
    const DUPLICATE = true;
    const DELETE = true;

    public static function baseQuery($model): Builder
    {
        return $model::with('parent')->canHaveChildren();
    }

    public function selectOptions($field): Collection
    {
        switch ($field) {
            case 'parent_id': return Model::canBeParent()->get()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->getFullName($item),
                ];
            })->sortBy('name');
            default: return collect();
        }
    }

    public function topButtons(): array
    {
        return array_merge(
            array(
                'sync_new' => [
                    'show' => [
                        'active' => true,
                        'trash' => false,
                    ],
                    'icon' => 'bi bi-download',
                    'label' => 'Sync',
                    'type' => 'buttons.action-link',
                    'style' => '',
                    'route' => 'sync.categories',
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
                'route' => 'categories.show',
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