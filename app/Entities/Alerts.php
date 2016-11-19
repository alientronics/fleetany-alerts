<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Alerts extends Model
{
    use SoftDeletes;
    
    protected $table = 'alerts';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'alert_type_id', 'part_id', 'destination',
    ];
}
