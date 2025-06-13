<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_name',
        'product_reference',
        'quantity',
        'unit_price',
        'discount_percentage',
        'total',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'discount_percentage' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function calculateTotal(): void
    {
        $itemTotal = $this->quantity * $this->unit_price;
        $itemDiscount = $itemTotal * ($this->discount_percentage / 100);
        $this->total = $itemTotal - $itemDiscount;
        $this->save();
    }
} 