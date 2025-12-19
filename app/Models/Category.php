<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['name', 'type'];

    public function villasWithService()
    {
        return $this->belongsToMany(Villa::class, 'villa_special_categories');
    }
}


