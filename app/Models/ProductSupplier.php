<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ProductSupplier extends Pivot
{
    use HasUuids;

    protected $table = 'product_suppliers';

    protected $fillable = [
        'product_id',
        'supplier_id',
        'supplier_sku',
        'unit_cost',
    ];

    protected function casts()
    {
        return [
            'unit_cost' => 'decimal:2',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }
}
