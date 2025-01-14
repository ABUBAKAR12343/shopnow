<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Auth\User as Authenticatable;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
use Laravel\Sanctum\HasApiTokens;

class Role extends Eloquent
{
    use HasApiTokens, HasFactory;
    protected $connection = 'mongodb';
    protected $collection = 'roles';

    protected $guarded = ['id'];


    public function users()
    {
        return $this->hasMany(User::class, 'role_id', '_id');
    }
}
