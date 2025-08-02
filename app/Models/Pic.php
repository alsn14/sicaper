<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pic extends Model
{
    use HasFactory;
    protected $table = 'pics';
    protected $fillable = [
        'nomer','name','nip'
    ];
    public function items():HasMany
    {
        return $this -> hasMany(Item::class);
    }
}

