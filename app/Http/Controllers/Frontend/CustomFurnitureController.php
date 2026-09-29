<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CustomFurnitureController extends Controller
{
    /**
     * Upload custom furniture sample/reference design image.
     */
    public function uploadDesign(Request $request): JsonResponse
    {
        $request->validate([
            'image' => [
                'required',
                'file',
                'image',
                'mimes:jpeg,jpg,png,webp',
                'max:5120', // max 5MB
            ],
        ], [
            'image.required' => 'File gambar contoh desain harus dipilih.',
            'image.image' => 'File yang diupload harus berupa gambar.',
            'image.mimes' => 'Format gambar yang didukung: JPG, PNG, atau WEBP.',
            'image.max' => 'Ukuran gambar maksimal adalah 5MB.',
        ]);

        $path = $request->file('image')->store('custom-designs', 'public');
        $url = asset(Storage::url($path));

        return response()->json([
            'success' => true,
            'message' => 'Gambar berhasil diunggah.',
            'path' => $path,
            'url' => $url,
        ]);
    }
}
