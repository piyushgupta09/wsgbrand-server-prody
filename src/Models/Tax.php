<?php

namespace Fpaipl\Prody\Models;

use Fpaipl\Panel\Traits\Authx;
use Fpaipl\Panel\Traits\HasActive;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tax extends Model
{
    use Authx,
        HasActive,
        SoftDeletes,
        LogsActivity;

    protected $table = 'taxes';

    protected $fillable = [
        'name',
        'hsncode',
        'gstrate',
    ];

    protected static $logName = 'taxation';

    public static function validationRules()
    {
        return [
            'name' => 'required|string|max:255',
            'hsncode' => 'required|string|min:4|max:12',
            'gstrate' => 'required|numeric',
        ];
    }

    protected static function booted()
    {    
        static::saved(function ($model) {
            $tags = array();
            array_push($tags, $model->name);
            array_push($tags, $model->hsncode);
            array_push($tags, $model->gstrate);
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
