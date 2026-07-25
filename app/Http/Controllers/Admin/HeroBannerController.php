<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreHeroBannerRequest;
use App\Http\Requests\Admin\UpdateHeroBannerRequest;
use App\Models\HeroBanner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class HeroBannerController extends Controller
{
    /**
     * Display a paginated listing of hero banners.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $statusFilter = $request->input('status');

        $heroes = HeroBanner::query()
            ->when($search, function ($query, $search) {
                $query->where('title', 'like', "%{$search}%")
                    ->orWhere('subtitle', 'like', "%{$search}%")
                    ->orWhere('badge_text', 'like', "%{$search}%");
            })
            ->when($statusFilter, function ($query, $statusFilter) {
                $query->where('status', $statusFilter);
            })
            ->orderBy('sort_order')
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('admin.hero-banners.index', compact('heroes', 'search', 'statusFilter'));
    }

    /**
     * Show the form for creating a new hero banner.
     */
    public function create()
    {
        return view('admin.hero-banners.create');
    }

    /**
     * Store a newly created hero banner in storage.
     */
    public function store(StoreHeroBannerRequest $request)
    {
        $data = $request->validated();

        // Upload image
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('heroes', 'public');
        }

        // Create hero
        $hero = HeroBanner::create($data);

        // Ensure single active
        $hero->ensureSingleActive();

        return redirect()
            ->route('admin.hero-banners.index')
            ->with('success', 'Hero banner "' . $data['title'] . '" berhasil ditambahkan.');
    }

    /**
     * Display the specified hero banner.
     */
    public function show(HeroBanner $heroBanner)
    {
        return view('admin.hero-banners.show', ['hero' => $heroBanner]);
    }

    /**
     * Show the form for editing the specified hero banner.
     */
    public function edit(HeroBanner $heroBanner)
    {
        return view('admin.hero-banners.edit', ['hero' => $heroBanner]);
    }

    /**
     * Update the specified hero banner in storage.
     */
    public function update(UpdateHeroBannerRequest $request, HeroBanner $heroBanner)
    {
        $data = $request->validated();

        // Upload new image if provided
        if ($request->hasFile('image')) {
            // Delete old image
            if ($heroBanner->image) {
                Storage::disk('public')->delete($heroBanner->image);
            }
            $data['image'] = $request->file('image')->store('heroes', 'public');
        } else {
            unset($data['image']);
        }

        // Update hero
        $heroBanner->update($data);

        // Ensure single active
        $heroBanner->ensureSingleActive();

        return redirect()
            ->route('admin.hero-banners.index')
            ->with('success', 'Hero banner "' . $data['title'] . '" berhasil diperbarui.');
    }

    /**
     * Remove the specified hero banner from storage.
     */
    public function destroy(HeroBanner $heroBanner)
    {
        $name = $heroBanner->title;
        $heroBanner->delete(); // Image cleanup in model boot

        return redirect()
            ->route('admin.hero-banners.index')
            ->with('success', 'Hero banner "' . $name . '" berhasil dihapus.');
    }

    /**
     * Toggle the status of a hero banner.
     */
    public function toggleStatus(HeroBanner $heroBanner)
    {
        $heroBanner->status = $heroBanner->status === 'active' ? 'inactive' : 'active';
        $heroBanner->save();

        // Ensure single active
        $heroBanner->ensureSingleActive();

        $statusLabel = $heroBanner->status_label;

        return redirect()
            ->route('admin.hero-banners.index')
            ->with('success', 'Hero banner "' . $heroBanner->title . '" berhasil diubah menjadi ' . $statusLabel . '.');
    }
}

