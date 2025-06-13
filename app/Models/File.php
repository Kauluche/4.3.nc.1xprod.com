<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    protected $fillable = ['pharmacy_id', 'path', 'name', 'type', 'size'];

    public function pharmacy()
    {
        return $this->belongsTo(Pharmacy::class);
    }
}