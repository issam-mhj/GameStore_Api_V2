<?php

namespace Tests\Unit\product;

use App\Models\Category;
use App\Models\Image;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Mockery;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class storeProductTest extends TestCase
{
    use RefreshDatabase, WithFaker;
    #[Test]
    public function user_without_permission_cannot_create_products()
    {
        $user = User::factory()->create();

        Gate::define('create_products', function () {
            return false;
        });

        $this->actingAs($user);

        $response = $this->postJson('/api/products', [
            'name' => 'Test Product',
            'slug' => 'test-product',
            'description' => 'Test description',
            'price' => 99.99,
            'stock' => 10,
            'category_id' => 1,
            'images' => [],
            'primary_index' => 0
        ]);

        $response->assertStatus(403)
            ->assertJson([
                'message' => 'You do not have permission to create products'
            ]);
    }

    #[Test]
    public function validation_rejects_invalid_product_data()
    {
        $user = User::factory()->create();

        Gate::define('create_products', function () {
            return true;
        });

        $this->actingAs($user);


        $response = $this->postJson('/api/products', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'slug', 'price', 'stock', 'category_id', 'images', 'primary_index']);
    }

    #[Test]
    public function product_is_successfully_created_with_valid_data()
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();

        Gate::define('create_products', function () {
            return true;
        });

        $this->actingAs($user);

        // Setup test files
        Storage::fake('public');
        $image1 = UploadedFile::fake()->image('product1.jpg');
        $image2 = UploadedFile::fake()->image('product2.jpg');

        $response = $this->postJson('/api/products', [
            'name' => 'New Game',
            'slug' => 'new-game',
            'description' => 'A great new game',
            'price' => 59.99,
            'stock' => 25,
            'category_id' => $category->id,
            'images' => [$image1, $image2],
            'primary_index' => 0
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'product',
                'images'
            ])
            ->assertJson([
                'message' => 'Product created successfully'
            ]);


        $this->assertDatabaseHas('products', [
            'name' => 'New Game',
            'slug' => 'new-game',
            'price' => 59.99,
            'stock' => 25,
            'category_id' => $category->id,
        ]);


        $product = Product::where('name', 'New Game')->first();
        $this->assertEquals(2, $product->images()->count());


        $this->assertEquals(1, $product->images()->where('is_primary', true)->count());


        Storage::disk('public')->assertExists('products/' . basename($product->images[0]->image_url));
        Storage::disk('public')->assertExists('products/' . basename($product->images[1]->image_url));
    }

    #[Test]
    public function duplicate_product_name_is_rejected()
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();


        $existingProduct = Product::factory()->create([
            'name' => 'Existing Game',
            'slug' => 'existing-game',
            'category_id' => $category->id
        ]);

        Gate::define('create_products', function () {
            return true;
        });

        $this->actingAs($user);

        Storage::fake('public');
        $image = UploadedFile::fake()->image('product.jpg');


        $response = $this->postJson('/api/products', [
            'name' => 'Existing Game',
            'slug' => 'new-game-slug',
            'description' => 'Another game',
            'price' => 29.99,
            'stock' => 10,
            'category_id' => $category->id,
            'images' => [$image],
            'primary_index' => 0
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }

}
