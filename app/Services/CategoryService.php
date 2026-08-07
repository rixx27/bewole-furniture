<?php

namespace App\Services;

use App\Models\Category;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class CategoryService
{
    /**
     * Storage directory for category cover images.
     */
    public const UPLOAD_DIR = 'categories';

    /**
     * Create a new category with optional cover upload.
     *
     * @param  array<string, mixed>  $data
     */
    public function store(array $data, ?UploadedFile $cover = null): Category
    {
        if ($cover instanceof UploadedFile) {
            $data['cover_image'] = $cover->store(self::UPLOAD_DIR, 'public');
        }

        return Category::create($data);
    }

    /**
     * Update an existing category, replacing cover only when a new one is uploaded.
     *
     * @param  array<string, mixed>  $data
     */
    public function update(Category $category, array $data, ?UploadedFile $cover = null): Category
    {
        if ($cover instanceof UploadedFile) {
            if ($category->cover_image) {
                Storage::disk('public')->delete($category->cover_image);
            }
            $data['cover_image'] = $cover->store(self::UPLOAD_DIR, 'public');
        }

        $category->update($data);

        return $category;
    }

    /**
     * Delete a category. Cover image is cleaned up by the model's deleted hook.
     */
    public function delete(Category $category): void
    {
        $category->delete();
    }
}
