<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StockMovementResource extends JsonResource
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
            'type' => $this->type,  
            'quantity' => $this->quantity,  
            'previous_quantity' => $this->previous_quantity,  
            'new_quantity' => $this->new_quantity,  
            'reason' => $this->reason,  
            'reference' => $this->reference,  
            'created_at' => $this->created_at,  
            'updated_at' => $this->updated_at,
            
            'product' => ProductResource::make(
                $this->whenLoaded('product')
            ),

            'user' => UserResource::make(
                $this->whenLoaded('user')
            ),
        ];
    }
}
