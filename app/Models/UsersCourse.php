<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Course;
use App\User;
use App\Models\Unit;
use App\Models\UsersCourseUnit;

class UsersCourse extends Model
{
    protected $table = "users_courses";
    protected $fillable = [
        'user_id',
        'course_id',
    ];

    public function courses()
    {
        return $this->hasMany(Course::class, 'id', 'course_id')->with('units');
    }

    public function units()
    {
        return $this->hasMany(Course::class, 'id', 'course_id')->with('units');
    }

    public function completedUnits()
    {
        return $this->hasMany(UsersCourseUnit::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
