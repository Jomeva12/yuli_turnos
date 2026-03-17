<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShiftTemplate extends Model
{
    use HasFactory;

    protected $fillable = ['day_of_week', 'area_id', 'schedule', 'type', 'required_count'];

    public function area()
    {
        return $this->belongsTo(Area::class);
    }
}
