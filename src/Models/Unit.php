<?php

namespace Fpaipl\Prody\Models;

use Fpaipl\Panel\Traits\Authx;
use Fpaipl\Panel\Traits\HasActive;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class Unit extends Model
{
    use Authx, 
        HasActive,
        SoftDeletes,
        LogsActivity;

    protected $fillable = [
        'name',
        'names',
        'abbr',
        'abbrs',
    ];

    protected static $logName = 'unit';

    public static function validationRules()
    {
        return [
            'name' => 'required|string|max:255',
            'names' => 'required|string|max:255',
            'abbr' => 'required|string|max:255',
            'abbrs' => 'required|string|max:255',
        ];
    }

    protected static function booted()
    {    
        static::saved(function ($model) {
            $tags = array();
            array_push($tags, $model->name);
            array_push($tags, $model->abbr);
            $model->tags = implode(',', $tags);
            $model->saveQuietly();
        });
    }

    public function getTableData($key)
    {
        switch ($key) {
            default: return $this->{$key};
        }
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logOnly($this->fillable);
    }
}
