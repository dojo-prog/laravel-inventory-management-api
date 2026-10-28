<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class ProductSupplier extends Model
{
    use HasUuids;

    protected $fillable = [
        'product_id',
        'supplier_id',
        'supplier_sku',
        'unit_cost', 
    ];

    protected function casts()
    {
        return [
            'unit_cost' => 'decimal:2'
        ];
    }
}
