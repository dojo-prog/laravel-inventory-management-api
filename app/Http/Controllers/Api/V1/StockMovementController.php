<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StockMovement\IndexStockMovementRequest;
use App\Http\Requests\StockMovement\StoreStockMovementRequest;
use App\Http\Resources\StockMovementResource;
use App\Models\StockMovement;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class StockMovementController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     */
    public function index(IndexStockMovementRequest $request)
    {
        $this->authorize('viewAny', StockMovement::class);

        $filters = $request->validated();

        $query = StockMovement::query()
            ->when(
                $filters['product_id'] ?? null,
                fn ($query, $product_id) =>
                    $query->where('product_id', $product_id)
            )
            ->when(
                $filters['user_id'] ?? null,
                fn ($query, $user_id) =>
                    $query->where('user_id', $user_id)
            )
            ->when(
                $filters['type'] ?? null,
                fn ($query, $type) =>
                    $query->where('type', $type)
            )
            ->when(
                $filters['from'] ?? null,
                fn ($query, $from) =>
                    $query->whereDate('created_at', '>=', $from)
            )
            ->when(
                $filters['to'] ?? null,
                fn ($query, $to) =>
                    $query->whereDate('created_at', '<=', $to)
            );

        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortOrder = $filters['sort_order'] ?? 'desc';

        $stockMovements = $query
            ->with(['product', 'user'])
            ->orderBy($sortBy, $sortOrder)
            ->paginate($filters['per_page'] ?? 15);

        return StockMovementResource::collection($stockMovements);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreStockMovementRequest $request)
    {
      //
    }

    /**
     * Display the specified resource.
     */
    public function show(StockMovement $stockMovement)
    {
        $this->authorize('view', $stockMovement);

        $stockMovement->load(['product', 'user']);

        return new StockMovementResource($stockMovement);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, StockMovement $stockMovement)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(StockMovement $stockMovement)
    {
        //
    }
}
