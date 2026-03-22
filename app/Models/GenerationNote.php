<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GenerationNote extends Model
{
    protected $fillable = ['employee_id', 'date', 'message', 'type'];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
