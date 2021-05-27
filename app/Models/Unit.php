<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\UnitFile;
use App\Models\Course;

class Unit extends Model
{
    protected $table = "units";
    protected $fillable = [
        'course_id',
        'code',
        'title',
        'description',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function files()
    {
        return $this->hasMany(UnitFile::class);
    }

    public function user()
    {
        return $this->hasMany(UsersCourse::class)->user;
    }

    public function enrolledUnits()
    {
        # code...
    }
}
