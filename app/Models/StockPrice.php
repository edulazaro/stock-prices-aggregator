<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockPrice extends Model
{
    use HasFactory;

    /** @var array The attributes that are mass assignable. */
    protected $fillable = [
        'symbol',
        'price',
        'volume',
        'open',
        'high',
        'low',
        'previous_close'
    ];

    /** @var array Attributes to hide from the model's array/JSON. */
    protected $hidden = [
        'id'
    ];
    
    /** @var array Attributes to be appended to the model's array/JSON. */
    protected $appends = [
        'percentage_change'
    ];

    /**
     * Get the percentage change between the current price and the previous close.
     *
     * @return float The percentage change.
     */
    public function getPercentageChangeAttribute(): float
    {
        $percentageChange = ($this->price - $this->previous_close) / $this->previous_close * 100;
        return round($percentageChange, 4);
    }
}
