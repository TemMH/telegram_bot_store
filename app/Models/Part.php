<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Part extends Model
{
    protected $primaryKey = 'part_code';
    public $incrementing = false;
    protected $keyType = 'string';

    use Searchable;

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


    public function toSearchableArray(): array
    {
        return [
            'part_code' => $this->part_code,
            'name' => $this->name,
            'applicability' => $this->applicability,
        ];
    }
}
