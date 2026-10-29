<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Supplier\IndexSupplierRequest;
use App\Http\Requests\Supplier\StoreSupplierRequest;
use App\Http\Requests\Supplier\UpdateSupplierRequest;
use App\Http\Resources\SupplierResource;
use App\Models\Supplier;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display a listing of the resource.
     */
    public function index(IndexSupplierRequest $request)
    {
        $this->authorize('viewAny', Supplier::class);

        $filters = $request->validated();

        $query = Supplier::query()
            ->with('products')
            ->when(
                $filters['search'] ?? null, 
                fn ($query, $search) =>
                    $query->where('name', 'ilike', "%{$search}%")
                    ->orWhere('contact_person', 'ilike', "%{$search}%")
                    ->orWhere('email', 'ilike', "%{$search}%")
            )
            ->when(
                $filters['product_id'] ?? null,
                fn ($query, $productId) =>
                    $query->whereHas('products', fn ($subQuery) =>
                        $subQuery->where('products.id', $productId)
                    )
            );

        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortOrder = $filters['sort_order'] ?? 'desc';

        $suppliers = $query
            ->orderBy($sortBy, $sortOrder)
            ->paginate($filters['per_page'] ?? 15);

        return SupplierResource::collection($suppliers);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSupplierRequest $request)
    {
        $this->authorize('create', Supplier::class);

        $supplier = Supplier::create($request->validated());
        $supplier->load('products');

        return (new SupplierResource($supplier))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Supplier $supplier)
    {
        $this->authorize('view', $supplier);
        
        $supplier->load('products');

        return new SupplierResource($supplier);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSupplierRequest $request, Supplier $supplier)
    {
        $this->authorize('view', $supplier);
        
        $supplier->update($request->validated());
        $supplier->load('products');

        return new SupplierResource($supplier);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Supplier $supplier)
    {
        $this->authorize('view', $supplier);
        
        $supplier->delete();

        return response()->noContent();
    }
}
