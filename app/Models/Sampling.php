<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sampling extends Model
{
    use HasFactory;
    protected $fillable = [
        'investor_id',
        'date_sampling',
        'doc',
        'cage_no',
    ];

    public function investor() {
        return $this->belongsTo(Investor::class);
    }

    public function samples() {
        return $this->hasMany(Sample::class);
    }
}
