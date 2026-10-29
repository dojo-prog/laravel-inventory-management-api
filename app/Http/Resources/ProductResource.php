<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'sku' => $this->sku,
            'name' => $this->name,
            'description' => $this->description,
            'unit' => $this->unit,
            'unit_cost' => $this->unit_cost,
            'stock_quantity' => $this->stock_quantity,
            'reorder_level' => $this->reorder_level,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            'category' => CategoryResource::make(
                $this->whenLoaded('category')
            ),
            'suppliers' => SupplierResource::collection(
                $this->whenLoaded('suppliers')
            )
        ];
    }
}
