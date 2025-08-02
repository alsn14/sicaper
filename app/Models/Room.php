<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Room extends Model
{
    use HasFactory;
    protected $table = 'rooms';
    protected $fillable = [
        'name','pic_id'
    ];
    public function items():HasMany
    {
        return $this -> hasMany(Item::class);
    }
    public function pic(): BelongsTo
    {
        return $this -> belongsTo(Pic::class ,'pic_id');
    }
}
