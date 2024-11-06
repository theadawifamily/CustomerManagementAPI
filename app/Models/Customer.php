<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class Customer extends Model
{
    use HasFactory;

    protected $keyType = 'string';
    public $incrementing = false;

    public const TIER_SILVER = 'Silver';
    public const TIER_GOLD = 'Gold';
    public const TIER_PLATINUM = 'Platinum';

    // Append the custom 'tier' attribute to the model's array form
    protected $appends = ['tier'];

    protected $fillable = [
        'id', // Add id here
        'name',
        'email',
        'annualSpend',
        'lastPurchaseDate',
    ];

    public function getTierAttribute(): string
    {
        if (is_null($this->annualSpend) || $this->annualSpend < 1000) {
            return self::TIER_SILVER;
        }

        $lastPurchaseDate = Carbon::parse($this->lastPurchaseDate);
        $now = Carbon::now();

        if ($this->annualSpend >= 10000 && $lastPurchaseDate->greaterThanOrEqualTo($now->subMonths(6))) {
            return self::TIER_PLATINUM;
        } elseif ($this->annualSpend >= 1000 && $this->annualSpend < 10000 && $lastPurchaseDate->greaterThanOrEqualTo($now->subMonths(12))) {
            return self::TIER_GOLD;
        }

        return self::TIER_SILVER;
    }
}
