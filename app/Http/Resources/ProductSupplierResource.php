<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductSupplierResource extends JsonResource
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
            'supplier_sku' => $this->supplier_sku,
            'unit_cost' => $this->unit_cost,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            
            'product' => ProductResource::make(
                $this->whenLoaded('product')
            ),

            'supplier' => SupplierResource::make(
                $this->whenLoaded('supplier')
            ),
        ];
    }
}
