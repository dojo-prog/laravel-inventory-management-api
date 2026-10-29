<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductSupplier\StoreProductSupplierRequest;
use App\Http\Requests\ProductSupplier\UpdateProductSupplierRequest;
use App\Http\Resources\ProductSupplierResource;
use App\Http\Resources\SupplierResource;
use App\Models\Product;
use App\Models\ProductSupplier;
use App\Models\Supplier;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ProductSupplierController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     */
    public function index(Product $product)
    {
        $this->authorize('viewAny', ProductSupplier::class);

        $productSuppliers = ProductSupplier::query()
            ->with(['product', 'supplier'])
            ->paginate(15);

        return ProductSupplierResource::collection($productSuppliers);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductSupplierRequest $request)
    {
        $this->authorize('create', ProductSupplier::class);

        $productSupplier = ProductSupplier::create($request->validated());
        $productSupplier->load(['product', 'supplier']);

        return (new ProductSupplierResource($productSupplier))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product, Supplier $supplier) 
    {
        $productSupplier = ProductSupplier::query()
            ->with(['product', 'supplier'])
            ->where('product_id', $product->id)
            ->where('supplier_id', $supplier->id)
            ->firstOrFail();

        $this->authorize('view', $productSupplier);

        return new ProductSupplierResource($productSupplier);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(
        UpdateProductSupplierRequest $request, 
        Product $product, 
        Supplier $supplier
    )
    {
        $productSupplier = ProductSupplier::query()
            ->where('product_id', $product->id)
            ->where('supplier_id', $supplier->id)
            ->firstOrFail();

        $this->authorize('update', $productSupplier);

        $productSupplier->update($request->validated());
        $productSupplier->load(['product', 'supplier']);

        return new ProductSupplierResource($productSupplier);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product, Supplier $supplier)
    {
        $productSupplier = ProductSupplier::query()
            ->where('product_id', $product->id)
            ->where('supplier_id', $supplier->id)
            ->firstOrFail();

        $this->authorize('delete', $productSupplier);

        $productSupplier->delete();

        return response()->noContent(); 
    }
}