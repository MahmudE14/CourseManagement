<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Role;

class UserRole extends Model
{
    protected $table = "user_roles";
    protected $fillable = [
        'user_id',
        'role_id',
    ];

    /**
     * Returns role details
     * @return belongsTo App\Models\UserRole
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }
}
