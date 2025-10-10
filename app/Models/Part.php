<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Part extends Model
{
    protected $table = 'parts';
    protected $fillable = [
        'part_code',
        'path_to_photo1',
        'path_to_photo2',
        'path_to_photo3',
        'name',
        'applicability',
        'original_code',
        'factory',
        'country',
        'quantity'
    ];
}
