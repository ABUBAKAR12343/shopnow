<?php

namespace App\Models;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Auth\User as Authenticatable;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
use Laravel\Sanctum\HasApiTokens;

class Category extends Eloquent
{
    use HasApiTokens, HasFactory;
    protected $connection = 'mongodb';
    protected $collection = 'categories';

    protected $guarded = ['id'];

    public function products()
    {
        return $this->hasMany(Product::class, 'category_type', '_id');
    }


}
