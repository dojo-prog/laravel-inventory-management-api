<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StockMovement\StoreStockMovementRequest;
use App\Http\Resources\ProductResource;
use App\Http\Resources\StockMovementResource; 
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\DB;

class ProductQuantityController extends Controller
{
    use AuthorizesRequests;

    public function stockIn(StoreStockMovementRequest $request, Product $product)
    {
        $this->authorize('create', StockMovement::class);

        $validated = $request->validated();
        $quantity = $validated['quantity'];

        $validated['user_id'] = $request->user()->id;
        $validated['product_id'] = $product->id;
        $validated['previous_quantity'] = $product->stock_quantity;
        $validated['new_quantity'] = $product->stock_quantity + $quantity;
        $validated['type'] = 'stock-in';

        [$product, $stockMovement] = DB::transaction(function () use ($validated, $product) {
            // Update Product Stock Quantity 
            $product->update(['stock_quantity' => $validated['new_quantity']]);

            // Record Stock Movement audit log
            $stockMovement = StockMovement::create($validated);

            return [$product, $stockMovement];
        });

        $product->load(['category', 'suppliers']);
        $stockMovement->load(['product', 'user']);
        
        return response()->json([
            'data' => [ 
                'product' => new ProductResource($product),
                'stock_movement' => new StockMovementResource($stockMovement)
            ]
        ], 201);
    }

    public function stockOut(StoreStockMovementRequest $request, Product $product)
    {
        $this->authorize('create', StockMovement::class);

        $validated = $request->validated();
        $quantity = $validated['quantity'];

        if (($product->stock_quantity - $quantity) < 0) {
            return response()->json([
                'message' => 'Insufficient stock available for this operation.'
            ], 422);
        }

        $validated['user_id'] = $request->user()->id;
        $validated['product_id'] = $product->id;
        $validated['previous_quantity'] = $product->stock_quantity;
        $validated['new_quantity'] = $product->stock_quantity - $quantity;
        $validated['type'] = 'stock-out';

        [$product, $stockMovement] = DB::transaction(function () use ($validated, $product) {
            $product->update(['stock_quantity' => $validated['new_quantity']]);
            $stockMovement = StockMovement::create($validated);

            return [$product, $stockMovement];
        });

        $product->load(['category', 'suppliers']);
        $stockMovement->load(['product', 'user']);
        
        return response()->json([
            'data' => [ 
                'product' => new ProductResource($product),
                'stock_movement' => new StockMovementResource($stockMovement)
            ]
        ], 201);
    }

    public function adjust(StoreStockMovementRequest $request, Product $product)
    {
        $this->authorize('create', StockMovement::class);

        $validated = $request->validated();
        $newStock = $validated['quantity']; 

        $validated['user_id'] = $request->user()->id;
        $validated['product_id'] = $product->id;
        $validated['previous_quantity'] = $product->stock_quantity;
        $validated['new_quantity'] = $newStock;
        $validated['type'] = 'adjustment';
       

        [$product, $stockMovement] = DB::transaction(function () use ($validated, $product, $newStock) {
            $product->update(['stock_quantity' => $newStock]);
            $stockMovement = StockMovement::create($validated);

            return [$product, $stockMovement];
        });

        $product->load(['category', 'suppliers']);
        $stockMovement->load(['product', 'user']);
        
        return response()->json([
            'data' => [ 
                'product' => new ProductResource($product),
                'stock_movement' => new StockMovementResource($stockMovement)
            ]
        ], 201);
    }
}