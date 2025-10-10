<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vin extends Model
{
    protected $table = 'vin_dataset';
    protected $fillable = [
        'make',
        'wmi',
        'model',
        'vds',
        'year',
        'vis',
    ];


}
