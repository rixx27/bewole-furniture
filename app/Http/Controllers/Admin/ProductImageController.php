<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductImageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.placeholder', [
            'title' => __('Product Gallery'),
            'description' => __('Manage product images and galleries.'),
        ]);
    }

    /**
     * Remove the specified product image from storage.
     */
    public function destroy(Request $request, ProductImage $productImage)
    {
        try {
            if ($productImage->image && Storage::disk('public')->exists($productImage->image)) {
                Storage::disk('public')->delete($productImage->image);
            }

            $productImage->delete();

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Foto galeri berhasil dihapus.',
                ]);
            }

            return back()->with('success', 'Foto galeri berhasil dihapus.');
        } catch (\Throwable $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menghapus foto galeri: ' . $e->getMessage(),
                ], 500);
            }

            return back()->with('error', 'Gagal menghapus foto galeri.');
        }
    }
}


