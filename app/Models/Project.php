<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;
    
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
}