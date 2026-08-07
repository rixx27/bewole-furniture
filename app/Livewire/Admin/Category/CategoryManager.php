<?php

namespace App\Livewire\Admin\Category;

use App\Models\Category;
use App\Services\CategoryService;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.admin')]
class CategoryManager extends Component
{
    use WithFileUploads;

    /**
     * The service instance.
     */
    protected CategoryService $categoryService;

    // Listing state
    public string $search = '';
    public array $categories = [];

    // Form state
    public ?int $editingId = null;
    public bool $editing = false;
    public string $name = '';
    public string $short_description = '';
    public bool $is_active = true;
    public int $sort_order = 0;

    // Cover upload state
    public $cover_image = null;
    public ?string $cover_preview = null;
    public ?string $existing_cover = null;

    /**
     * Loading state.
     */
    public bool $isLoading = false;

    /**
     * Boot the component with the category service.
     */
    public function boot(CategoryService $categoryService): void
    {
        $this->categoryService = $categoryService;
    }

    /**
     * Load the category listing.
     */
    public function mount(): void
    {
        $this->loadCategories();
    }

    /**
     * Load categories into the listing state.
     */
    protected function loadCategories(): void
    {
        $this->categories = Category::query()
            ->when($this->search !== '', function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', "%{$this->search}%")
                        ->orWhere('code', 'like', "%{$this->search}%")
                        ->orWhere('short_description', 'like', "%{$this->search}%");
                });
            })
            ->sorted()
            ->get()
            ->map(fn (Category $category) => [
                'id' => $category->id,
                'code' => $category->code,
                'name' => $category->name,
                'slug' => $category->slug,
                'cover_image' => $category->cover_image,
                'cover_image_url' => $category->cover_image_url,
                'sort_order' => $category->sort_order,
                'is_active' => $category->is_active,
                'short_description' => $category->short_description,
                'products_count' => $category->products()->count(),
            ])
            ->all();
    }

    /**
     * Live search.
     */
    public function updatedSearch(): void
    {
        $this->loadCategories();
    }

    /**
     * Reset the form to create mode.
     */
    public function openCreate(): void
    {
        $this->resetForm();
        $this->editing = true;
        $this->editingId = null;
    }

    /**
     * Load a category into the form for editing.
     */
    public function openEdit(int $categoryId): void
    {
        $category = Category::findOrFail($categoryId);

        $this->editingId = $category->id;
        $this->editing = true;
        $this->name = $category->name;
        $this->short_description = $category->short_description ?? '';
        $this->is_active = (bool) $category->is_active;
        $this->sort_order = (int) $category->sort_order;
        $this->existing_cover = $category->cover_image;
        $this->cover_preview = $category->cover_image_url;
        $this->cover_image = null;
    }

    /**
     * Close the form and return to listing.
     */
    public function closeForm(): void
    {
        $this->editing = false;
        $this->editingId = null;
        $this->resetForm();
    }

    /**
     * Reset the form fields.
     */
    protected function resetForm(): void
    {
        $this->name = '';
        $this->short_description = '';
        $this->is_active = true;
        $this->sort_order = 0;
        $this->cover_image = null;
        $this->cover_preview = null;
        $this->existing_cover = null;
    }

    /**
     * Generate a preview when a cover image is selected.
     */
    public function updatedCoverImage(): void
    {
        $this->validateOnly('cover_image', [
            'cover_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $this->cover_preview = $this->cover_image->temporaryUrl();
    }

    /**
     * Remove the currently selected / existing cover.
     */
    public function removeCover(): void
    {
        $this->cover_image = null;
        $this->cover_preview = null;
        $this->existing_cover = null;
    }

    /**
     * Save a new or existing category.
     */
    public function save(): void
    {
        $this->isLoading = true;

        try {
            $this->validate();

            $data = [
                'name' => $this->name,
                'short_description' => $this->short_description === '' ? null : $this->short_description,
                'is_active' => $this->is_active,
                'sort_order' => $this->sort_order,
            ];

            if ($this->editingId) {
                $category = Category::findOrFail($this->editingId);
                $this->categoryService->update($category, $data, $this->cover_image);
                $message = 'Kategori "' . $this->name . '" berhasil diperbarui.';
            } else {
                $data['slug'] = Str::slug($this->name);
                $this->categoryService->store($data, $this->cover_image);
                $message = 'Kategori "' . $this->name . '" berhasil ditambahkan.';
            }

            $this->closeForm();
            $this->loadCategories();
            $this->dispatch('category-saved', type: 'success', message: $message);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->dispatch('category-saved', type: 'error', message: 'Validasi gagal. Periksa kembali input Anda.');
            throw $e;
        } catch (\Exception $e) {
            $this->dispatch('category-saved', type: 'error', message: 'Terjadi kesalahan: ' . $e->getMessage());
        } finally {
            $this->isLoading = false;
        }
    }

    /**
     * Toggle a category's active status.
     */
    public function toggleActive(int $categoryId): void
    {
        $category = Category::findOrFail($categoryId);
        $category->is_active = !$category->is_active;
        $category->save();

        $status = $category->is_active ? 'diaktifkan' : 'dinonaktifkan';
        $this->loadCategories();
        $this->dispatch('category-saved', type: 'success', message: "Kategori \"{$category->name}\" berhasil {$status}.");
    }

    /**
     * Delete a category.
     */
    public function delete(int $categoryId): void
    {
        $category = Category::findOrFail($categoryId);

        if ($category->products()->count() > 0) {
            $this->dispatch('category-saved', type: 'error',
                message: "Kategori \"{$category->name}\" tidak dapat dihapus karena memiliki {$category->products()->count()} produk.");
            return;
        }

        $name = $category->name;
        $this->categoryService->delete($category);
        $this->loadCategories();
        $this->dispatch('category-saved', type: 'success', message: "Kategori \"{$name}\" berhasil dihapus.");
    }

    /**
     * Validation rules.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    protected function rules(): array
    {
        $nameRules = ['required', 'string', 'max:255'];

        if ($this->editingId) {
            $nameRules[] = Rule::unique('categories', 'name')->ignore($this->editingId);
        } else {
            $nameRules[] = Rule::unique('categories', 'name');
        }

        $rules = [
            'name' => $nameRules,
            'short_description' => ['nullable', 'string', 'max:500'],
            'is_active' => ['boolean'],
            'sort_order' => ['integer', 'min:0', 'max:9999'],
            'cover_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];

        // Cover wajib saat create
        if (!$this->editingId) {
            $rules['cover_image'][] = 'required';
        }

        return $rules;
    }

    /**
     * Custom validation messages.
     *
     * @return array<string, string>
     */
    protected function messages(): array
    {
        return [
            'name.required' => 'Nama kategori wajib diisi.',
            'name.unique' => 'Nama kategori sudah digunakan.',
            'name.max' => 'Nama kategori maksimal 255 karakter.',
            'short_description.max' => 'Deskripsi singkat maksimal 500 karakter.',
            'sort_order.integer' => 'Urutan harus berupa angka.',
            'sort_order.min' => 'Urutan minimal 0.',
            'sort_order.max' => 'Urutan maksimal 9999.',
            'cover_image.image' => 'Cover harus berupa gambar.',
            'cover_image.mimes' => 'Cover harus berformat jpg, jpeg, png, atau webp.',
            'cover_image.max' => 'Ukuran cover maksimal 2 MB.',
            'cover_image.required' => 'Cover wajib diunggah.',
        ];
    }

    /**
     * Render the component.
     */
    public function render()
    {
        return view('livewire.admin.category.category-manager');
    }
}
