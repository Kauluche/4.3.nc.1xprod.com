<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'pharmacy_id',
        'commercial_id',
        'status',
        'total',
        'discount',
        'notes',
    ];

    protected $casts = [
        'total' => 'decimal:2',
        'discount' => 'decimal:2',
    ];

    public function pharmacy(): BelongsTo
    {
        return $this->belongsTo(Pharmacy::class);
    }

    public function commercial(): BelongsTo
    {
        return $this->belongsTo(User::class, 'commercial_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function calculateTotals(): void
    {
        $total = 0;
        $totalDiscount = 0;

        foreach ($this->items as $item) {
            $itemTotal = $item->quantity * $item->unit_price;
            $itemDiscount = $itemTotal * ($item->discount_percentage / 100);

            $item->update([
                'total' => $itemTotal - $itemDiscount,
            ]);

            $total += $itemTotal;
            $totalDiscount += $itemDiscount;
        }

        $this->update([
            'total' => $total,
            'discount' => $totalDiscount,
        ]);
    }
}