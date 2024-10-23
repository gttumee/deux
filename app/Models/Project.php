<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Project extends Model
{
    use HasFactory,LogsActivity;
    
    protected $fillable = [
        'name',
        'explanation',
        'status',
        'start_date',
        'end_date',
    ];
    
    public function order()
    {
        return $this->hasMany(Order::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logOnly(['name', 'explanation','status', 'end_date'])
        ->dontLogIfAttributesChangedOnly(['text'])
        ->logOnlyDirty();
    }
}