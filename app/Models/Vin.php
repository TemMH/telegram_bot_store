<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vin extends Model
{
    protected $table = 'vin_dataset';
    protected $fillable = [
        'MAKE',
        'WMI',
        'model',
        'VDS',
        'year',
        'VIS',
    ];


}
