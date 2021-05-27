<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Unit;
use App\Models\UsersCourse;
use Illuminate\Support\Facades\Auth;
use App\Models\UsersCourseUnit;

class Course extends Model
{
    protected $table = "courses";
    protected $fillable = [
        'code',
        'title',
        'description',
        'thumbnail',
    ];

    public function units()
    {
        return $this->hasMany(Unit::class);
    }

    public function userCourse()
    {
        return $this->belongsTo(UsersCourse::class, 'id', 'course_id');
    }

    public function enrolledCourse()
    {
        return $this->belongsTo(UsersCourse::class, 'id', 'course_id')->where('user_id', Auth::user()->id);
    }

    public function completedUnits()
    {
        return $this->hasMany(UsersCourseUnit::class, 'course_id', 'id')->where('user_id', Auth::user()->id);
    }
}
