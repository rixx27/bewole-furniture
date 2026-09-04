<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProductRequest;
use App\Http\Requests\Admin\UpdateProductRequest;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * Display a paginated, searchable listing of products.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $categoryFilter = $request->input('category');
        $statusFilter = $request->input('status');

        $products = Product::with('category')
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('slug', 'like', "%{$search}%")
                      ->orWhere('short_description', 'like', "%{$search}%");
                });
            })
            ->when($categoryFilter, function ($query, $categoryFilter) {
                $query->where('category_id', $categoryFilter);
            })
            ->when($statusFilter, function ($query, $statusFilter) {
                $query->where('status', $statusFilter);
            })
            ->orderBy('sort_order', 'asc')
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        $categories = Category::orderBy('name')->get();

        return view('admin.products.index', compact('products', 'search', 'categories', 'categoryFilter', 'statusFilter'));
    }

    /**
     * Show the form for creating a new product.
     */
    public function create()
    {
        $categories = Category::orderBy('name')->get();
        return view('admin.products.create', compact('categories'));
    }

    /**
     * Store a newly created product in storage.
     */
    public function store(StoreProductRequest $request)
    {
        $data = $request->validated();

        // Auto-generate slug if not provided
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        // Ensure unique slug
        $data['slug'] = $this->generateUniqueSlug($data['slug']);

        // Handle boolean
        $data['is_featured'] = $request->boolean('is_featured', false);


        // Upload thumbnail
        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $request->file('thumbnail')->store('products/thumbnails', 'public');
        }

        // Create product (final price auto-calculated in model boot)
        $product = Product::create($data);

        // Handle materials options
        if (isset($data['materials']) && is_array($data['materials'])) {
            foreach ($data['materials'] as $mat) {
                if (empty($mat['name'])) continue;
                $cleanedPrice = str_replace(['.', ','], ['', '.'], $mat['price_per_meter'] ?? 0);
                $product->materials()->create([
                    'type' => $mat['type'],
                    'name' => trim($mat['name']),
                    'price_per_meter' => (float) $cleanedPrice,
                    'is_active' => isset($mat['is_active']) ? (bool)$mat['is_active'] : true,
                ]);
            }
        }

        // Upload gallery images (optional)
        if ($request->hasFile('gallery')) {
            foreach ($request->file('gallery') as $index => $image) {
                $path = $image->store('products/gallery', 'public');
                $product->images()->create([
                    'image' => $path,
                    'is_primary' => false,
                    'sort_order' => $index,
                ]);
            }
        }

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Produk "' . $data['name'] . '" berhasil ditambahkan.');
    }

    /**
     * Display the specified product.
     */
    public function show(Product $product)
    {
        $product->load(['category', 'images', 'materials']);
        return view('admin.products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified product.
     */
    public function edit(Product $product)
    {
        $categories = Category::orderBy('name')->get();
        $product->load(['images', 'materials']);
        return view('admin.products.edit', compact('product', 'categories'));
    }

    /**
     * Update the specified product in storage.
     */
    public function update(UpdateProductRequest $request, Product $product)
    {
        $data = $request->validated();

        // Auto-generate slug if not provided
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        // Ensure unique slug (excluding current product)
        $data['slug'] = $this->generateUniqueSlug($data['slug'], $product->id);

        // Handle boolean
        $data['is_featured'] = $request->boolean('is_featured', false);

        // Upload new thumbnail if provided
        if ($request->hasFile('thumbnail')) {
            // Delete old thumbnail
            if ($product->thumbnail) {
                Storage::disk('public')->delete($product->thumbnail);
            }
            $data['thumbnail'] = $request->file('thumbnail')->store('products/thumbnails', 'public');
        } else {
            unset($data['thumbnail']);
        }

        // Delete images marked for deletion
        if ($request->has('deleted_images')) {
            $imagesToDelete = ProductImage::whereIn('id', $data['deleted_images'])
                ->where('product_id', $product->id)
                ->get();

            foreach ($imagesToDelete as $image) {
                Storage::disk('public')->delete($image->image);
                $image->delete();
            }
        }

        // Upload new gallery images
        if ($request->hasFile('gallery')) {
            $lastSortOrder = $product->images()->max('sort_order') ?? 0;
            foreach ($request->file('gallery') as $index => $image) {
                $path = $image->store('products/gallery', 'public');
                $product->images()->create([
                    'image' => $path,
                    'is_primary' => false,
                    'sort_order' => $lastSortOrder + $index + 1,
                ]);
            }
        }

        // Update product (final price auto-calculated in model boot)
        $product->update($data);

        // Handle product material options
        if (isset($data['materials']) && is_array($data['materials'])) {
            $keptIds = [];
            foreach ($data['materials'] as $mat) {
                if (empty($mat['name'])) continue;
                $cleanedPrice = str_replace(['.', ','], ['', '.'], $mat['price_per_meter'] ?? 0);
                
                if (!empty($mat['id'])) {
                    $matModel = $product->materials()->find($mat['id']);
                    if ($matModel) {
                        $matModel->update([
                            'type' => $mat['type'],
                            'name' => trim($mat['name']),
                            'price_per_meter' => (float) $cleanedPrice,
                            'is_active' => isset($mat['is_active']) ? (bool)$mat['is_active'] : true,
                        ]);
                        $keptIds[] = $matModel->id;
                        continue;
                    }
                }

                $newMat = $product->materials()->create([
                    'type' => $mat['type'],
                    'name' => trim($mat['name']),
                    'price_per_meter' => (float) $cleanedPrice,
                    'is_active' => isset($mat['is_active']) ? (bool)$mat['is_active'] : true,
                ]);
                $keptIds[] = $newMat->id;
            }
            $product->materials()->whereNotIn('id', $keptIds)->delete();
        }

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Produk "' . $data['name'] . '" berhasil diperbarui.');
    }

    /**
     * Remove the specified product from storage.
     */
    public function destroy(Product $product)
    {
        $name = $product->name;

        // Delete all gallery images
        foreach ($product->images as $image) {
            Storage::disk('public')->delete($image->image);
        }
        $product->images()->delete();

        // Thumbnail is deleted in model boot deleted event
        $product->delete();

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Produk "' . $name . '" berhasil dihapus.');
    }

    /**
     * Generate a unique slug by appending a counter if necessary.
     */
    private function generateUniqueSlug(string $slug, ?int $ignoreId = null): string
    {
        $original = $slug;
        $counter = 1;

        while (Product::where('slug', $slug)
            ->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))
            ->exists()
        ) {
            $slug = $original . '-' . $counter;
            $counter++;
        }

        return $slug;
    }
}

