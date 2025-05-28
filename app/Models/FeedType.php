<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FeedType extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'feed_type',
        'brand',
    ];

    protected $casts = [
        'deleted_at' => 'datetime',
    ];

    public function getDeletedAtAttribute($value)
    {
        if (!$value) {
            return null;
        }
        if ($value instanceof \DateTimeInterface) {
            return $value->format('Y-m-d H:i:s');
        }
        // If it's a string, convert to Carbon
        return \Carbon\Carbon::parse($value)->format('Y-m-d H:i:s');
    }
}
