<?php

use App\Livewire\Admin\Category\CategoryManager;
use App\Livewire\Frontend\ProductCatalog;
use App\Models\Category;
use App\Models\User;
use App\View\Components\Home\CategoryShowcase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function () {
    Storage::fake('public');
    Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
    $this->admin = User::factory()->create();
    $this->admin->assignRole('admin');
});

test('category model casts show_on_home as boolean and scopeShowOnHome works', function () {
    $cat1 = Category::create([
        'name' => 'Kategori Beranda',
        'slug' => 'kategori-beranda',
        'is_active' => true,
        'show_on_home' => true,
    ]);

    $cat2 = Category::create([
        'name' => 'Kategori Non-Beranda',
        'slug' => 'kategori-non-beranda',
        'is_active' => true,
        'show_on_home' => false,
    ]);

    expect($cat1->show_on_home)->toBeTrue()
        ->and($cat2->show_on_home)->toBeFalse();

    $homeCategories = Category::showOnHome()->pluck('id')->all();
    expect($homeCategories)->toContain($cat1->id)
        ->and($homeCategories)->not->toContain($cat2->id);
});

test('admin can see beranda toggle and label in category manager', function () {
    $this->actingAs($this->admin);

    Category::create([
        'name' => 'Sofa Mewah',
        'slug' => 'sofa-mewah',
        'is_active' => true,
        'show_on_home' => true,
    ]);

    Livewire::test(CategoryManager::class)
        ->assertSee('Beranda')
        ->assertSee('Tampil')
        ->call('openCreate')
        ->assertSee('Tampilan Beranda')
        ->assertSee('Tampilkan di Beranda');
});

test('admin can create category with show_on_home option', function () {
    $this->actingAs($this->admin);

    $file = UploadedFile::fake()->image('cover.jpg');

    Livewire::test(CategoryManager::class)
        ->call('openCreate')
        ->set('name', 'Kursi Tamu Eksklusif')
        ->set('cover_image', $file)
        ->set('is_active', true)
        ->set('show_on_home', true)
        ->call('save')
        ->assertHasNoErrors();

    $created = Category::where('name', 'Kursi Tamu Eksklusif')->first();
    expect($created)->not->toBeNull()
        ->and($created->is_active)->toBeTrue()
        ->and($created->show_on_home)->toBeTrue();
});

test('admin can toggle show_on_home directly from table list', function () {
    $this->actingAs($this->admin);

    $category = Category::create([
        'name' => 'Lemari Pakaian',
        'slug' => 'lemari-pakaian',
        'is_active' => true,
        'show_on_home' => false,
    ]);

    Livewire::test(CategoryManager::class)
        ->call('toggleShowOnHome', $category->id);

    expect($category->fresh()->show_on_home)->toBeTrue();

    Livewire::test(CategoryManager::class)
        ->call('toggleShowOnHome', $category->id);

    expect($category->fresh()->show_on_home)->toBeFalse();
});

test('status active and show_on_home operate independently', function () {
    $this->actingAs($this->admin);

    $category = Category::create([
        'name' => 'Meja Belajar',
        'slug' => 'meja-belajar',
        'is_active' => true,
        'show_on_home' => true,
    ]);

    // Deactivating category does not alter show_on_home preference
    Livewire::test(CategoryManager::class)
        ->call('toggleActive', $category->id);

    expect($category->fresh()->is_active)->toBeFalse()
        ->and($category->fresh()->show_on_home)->toBeTrue();
});

test('category showcase component on homepage only includes active categories marked show_on_home', function () {
    $catHomeActive = Category::create([
        'name' => 'Cat Home Active',
        'slug' => 'cat-home-active',
        'is_active' => true,
        'show_on_home' => true,
    ]);

    $catHomeInactive = Category::create([
        'name' => 'Cat Home Inactive',
        'slug' => 'cat-home-inactive',
        'is_active' => false,
        'show_on_home' => true,
    ]);

    $catNotHomeActive = Category::create([
        'name' => 'Cat Not Home Active',
        'slug' => 'cat-not-home-active',
        'is_active' => true,
        'show_on_home' => false,
    ]);

    $showcase = new CategoryShowcase();
    $ids = $showcase->categories->pluck('id')->all();

    expect($ids)->toContain($catHomeActive->id)
        ->and($ids)->not->toContain($catHomeInactive->id)
        ->and($ids)->not->toContain($catNotHomeActive->id);
});

test('product catalog includes active categories regardless of show_on_home setting', function () {
    $cat1 = Category::create([
        'name' => 'Catalog Cat 1',
        'slug' => 'catalog-cat-1',
        'is_active' => true,
        'show_on_home' => false,
    ]);

    $cat2 = Category::create([
        'name' => 'Catalog Cat 2',
        'slug' => 'catalog-cat-2',
        'is_active' => true,
        'show_on_home' => true,
    ]);

    $cat3 = Category::create([
        'name' => 'Catalog Cat 3 Inactive',
        'slug' => 'catalog-cat-3-inactive',
        'is_active' => false,
        'show_on_home' => true,
    ]);

    Livewire::test(ProductCatalog::class)
        ->assertSee('Catalog Cat 1')
        ->assertSee('Catalog Cat 2')
        ->assertDontSee('Catalog Cat 3 Inactive');
});

test('category with explicit sort_order 1 appears first from the left before categories with sort_order 0', function () {
    $catUnordered1 = Category::create([
        'name' => 'Dining Chair',
        'slug' => 'dining-chair',
        'sort_order' => 0,
        'is_active' => true,
        'show_on_home' => true,
    ]);

    $catUnordered2 = Category::create([
        'name' => 'Stool',
        'slug' => 'stool',
        'sort_order' => 0,
        'is_active' => true,
        'show_on_home' => true,
    ]);

    $catFirst = Category::create([
        'name' => 'Dining Table',
        'slug' => 'dining-table',
        'sort_order' => 1,
        'is_active' => true,
        'show_on_home' => true,
    ]);

    $catThird = Category::create([
        'name' => 'Book Case',
        'slug' => 'book-case',
        'sort_order' => 3,
        'is_active' => true,
        'show_on_home' => true,
    ]);

    $showcase = new CategoryShowcase();
    $orderedIds = $showcase->categories->pluck('id')->all();

    // Dining Table (sort_order = 1) must be first (index 0)
    expect($orderedIds[0])->toBe($catFirst->id)
        // Book Case (sort_order = 3) must be second
        ->and($orderedIds[1])->toBe($catThird->id)
        // Dining Chair and Stool (sort_order = 0) come after
        ->and($orderedIds[2])->toBe($catUnordered1->id)
        ->and($orderedIds[3])->toBe($catUnordered2->id);
});

