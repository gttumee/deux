<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Parallax\FilamentComments\Models\Traits\HasFilamentComments;


class Order extends Model
{
    use HasFactory;
    use HasFilamentComments;
    protected $fillable = [
        'name',
        'explanation',
        'status',
        'comment',
        'file_name',
        'start_date',
        'end_date',
        'end_time',
        'project_id',
        'user_id'
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}