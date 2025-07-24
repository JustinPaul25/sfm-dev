<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cage extends Model
{
    use HasFactory;
    protected $fillable = [
        'number_of_fingerlings',
        'feed_types_id',
        'investor_id',
    ];

    public function feedType() {
        return $this->belongsTo(FeedType::class, 'feed_types_id');
    }

    public function investor() {
        return $this->belongsTo(Investor::class);
    }

    public function feedConsumptions() {
        return $this->hasMany(CageFeedConsumption::class);
    }
} 