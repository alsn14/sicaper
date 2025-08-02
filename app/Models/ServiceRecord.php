<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'tanggal_service',
        'keterangan',
        'biaya',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}

