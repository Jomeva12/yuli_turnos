<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $fillable = ['name'];

    public function areas()
    {
        return $this->belongsToMany(Area::class);
    }
}
