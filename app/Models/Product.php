<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasUuids;

    protected static function booted()
    {
        static::creating(function ($product) {
            if (empty($product->sku)) {
                $product->sku = self::generateSKU($product);
            }
        });
    }

    public static function generateSKU(Product $product): string
    {
        $category = Category::find($product->category_id);
        $categoryName = $category ? $category->name : 'GEN';

        $categoryCode = strtoupper(substr($categoryName, 0, 3));
        $nameCode = strtoupper(substr($product->name, 0, 3));
        $uniqueSuffix = strtoupper(Str::random(4));

        return "{$categoryCode}-{$nameCode}-{$uniqueSuffix}";
    }

    protected $fillable = [
        'category_id',
        'sku',
        'name',
        'description',
        'image_url',
        'image_public_id',
        'unit',
        'unit_cost',
        'stock_quantity',
        'reorder_level',
        'status',
    ];

    protected function casts()
    {
        return [
            'unit_cost' => 'decimal:2',
            'stock_quantity' => 'integer',
            'reorder_level' => 'integer',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function suppliers(): BelongsToMany
    {
        return $this->belongsToMany(Supplier::class, 'product_suppliers')
            ->using(ProductSupplier::class)
            ->withPivot(['supplier_sku', 'unit_cost'])
            ->withTimestamps();
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }
}
