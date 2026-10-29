<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Supplier extends Model
{
    use HasUuids;

    protected $fillable = [
        'name',
        'contact_person',
        'email',
        'phone',
        'address'
    ];

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_suppliers')
            ->using(ProductSupplier::class)
            ->withPivot(['supplier_sku', 'unit_cost'])
            ->withTimestamps();
    }
}
