<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnitFile extends Model
{
    protected $table = "unit_files";
    protected $fillable = [
        'unit_id',
        'title',
        'location',
        'status',
    ];
}
