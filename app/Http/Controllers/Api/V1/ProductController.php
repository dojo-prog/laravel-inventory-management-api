<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Product\IndexProductRequest;
use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Jobs\DestroyCloudinaryImage;
use App\Models\Product;
use App\Services\CloudinaryService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ProductController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     */
    public function index(IndexProductRequest $request)
    {
        $this->authorize('viewAny', Product::class);

        $filters = $request->validated();

        $query = Product::query()
            ->with(['category', 'suppliers'])
            ->when(
                $filters['search'] ?? null, 
                fn ($query, $search) =>
                    $query->where(function ($sub) use ($search) {
                        $sub->where('name', 'ilike', "%{$search}%")
                            ->orWhere('sku', 'ilike', "%{$search}%");
                    })
            )
            ->when(
                $filters['category_id'] ?? null,
                fn ($query, $category_id) =>
                    $query->where('category_id', $category_id)
            )
            ->when(
                $filters['supplier_id'] ?? null,
                fn ($query, $supplier_id) =>
                    $query->whereHas('suppliers', fn ($subQuery) =>
                        $subQuery->where('suppliers.id', $supplier_id)
                    )
            )
            ->when(
                $filters['status'] ?? null,
                fn ($query, $status) =>
                    $query->where('status', $status)
            )
            ->when(
                $filters['min_stock'] ?? null,
                fn ($query, $min_stock) =>
                    $query->where('stock_quantity', '>=', $min_stock)
            )
            ->when(
                $filters['low_stock'] ?? false,
                fn ($query) =>
                    $query->whereColumn('stock_quantity', '<=', 'reorder_level')
            );

        $sortBy = $filters['sort_by'] ?? 'created_at'; 
        $sortOrder = $filters['sort_order'] ?? 'desc';

        $products = $query
            ->orderBy($sortBy, $sortOrder)
            ->paginate($filters['per_page'] ?? 15);

        return ProductResource::collection($products);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request, CloudinaryService $cloudinary)
    {
        $this->authorize('create', Product::class);

        $validated = $request->validated();

        if ($request->hasFile('image')) {
            $uploadResult = $cloudinary->upload($request->file('image'));
            $validated['image_url'] = $uploadResult['secure_url'];
            $validated['image_public_id'] = $uploadResult['public_id'];
        }

        $product = Product::create($validated);
        $product->load(['category', 'suppliers']);

        return (new ProductResource($product))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        $this->authorize('view', $product);
        $product->load(['category', 'suppliers']);

        return new ProductResource($product);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, Product $product, CloudinaryService $cloudinary)
    {
        $this->authorize('update', $product);

        $validated = $request->validated();
        $oldImagePublicId = $product->image_public_id;

        if ($request->hasFile('image')) {
            $uploadResult = $cloudinary->upload($request->file('image'));

            $validated['image_url'] = $uploadResult['secure_url'];
            $validated['image_public_id'] = $uploadResult['public_id'];
        }

        $product->update($validated);
        $product->load(['category', 'suppliers']);

        if ($request->hasFile('image') && $oldImagePublicId) {
            DestroyCloudinaryImage::dispatch($oldImagePublicId);
        }

        return new ProductResource($product);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $this->authorize('delete', $product);

        if ($product->image_public_id) {
            DestroyCloudinaryImage::dispatch($product->image_public_id);
        }

        $product->delete();

        return response()->noContent();
    }
}
