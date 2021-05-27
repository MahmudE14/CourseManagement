<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Unit;

class UsersCourseUnit extends Model
{
    protected $table = "users_course_units";
    protected $fillable = [
        'user_id',
        'course_id',
        'present_unit_id',
    ];

    public function unit()
    {
        return $this->hasMany(Unit::class, 'id', 'present_unit_id');
    }
}
