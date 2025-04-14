<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Permission;

class Role extends Model
{
    protected $table = 'roles';
    protected $fillable = ['name', 'guard_name' , 'permissions'];


    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_has_permissions');
    }

    
}
