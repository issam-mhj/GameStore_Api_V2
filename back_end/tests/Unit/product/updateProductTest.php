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

class UpdateProductTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function user_without_permission_cannot_update_products()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        Gate::define('edit_products', function () {
            return false;
        });

        $this->actingAs($user);

        $response = $this->putJson("/api/products/{$product->id}", [
            'name' => 'Updated Name',
            'slug' => 'updated-slug',
            'description' => 'Updated description',
            'price' => 129.99,
            'stock' => 15,
            'category_id' => $product->category_id,
        ]);

        $response->assertStatus(403)
            ->assertJson([
                'message' => 'You do not have permission to edit this product'
            ]);
    }

    #[Test]
    public function updating_non_existent_product_returns_404()
    {
        $user = User::factory()->create();

        Gate::define('edit_products', function () {
            return true;
        });

        $this->actingAs($user);

        $nonExistentId = 99999;

        $response = $this->putJson("/api/products/{$nonExistentId}", [
            'name' => 'Updated Name',
            'slug' => 'updated-slug',
            'description' => 'Updated description',
            'price' => 129.99,
            'stock' => 15,
            'category_id' => 1,
        ]);

        $response->assertStatus(404)
            ->assertJson([
                'message' => 'Selected product does not exist',
                'status' => 'error 404'
            ]);
    }

    #[Test]
    public function validation_rejects_invalid_product_data()
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();
        $product = Product::factory()->create([
            'category_id' => $category->id
        ]);

        Gate::define('edit_products', function () {
            return true;
        });

        $this->actingAs($user);

        // Invalid data: negative price and stock
        $response = $this->putJson("/api/products/{$product->id}", [
            'name' => 'Updated Name',
            'slug' => 'updated-slug',
            'description' => 'Updated description',
            'price' => -10, // invalid: negative price
            'stock' => -5, // invalid: negative stock
            'category_id' => $category->id,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['price', 'stock']);
    }

    #[Test]
    public function product_basic_data_is_updated_successfully()
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();
        $newCategory = Category::factory()->create();

        $product = Product::factory()->create([
            'name' => 'Original Name',
            'slug' => 'original-slug',
            'description' => 'Original description',
            'price' => 99.99,
            'stock' => 10,
            'category_id' => $category->id
        ]);

        Gate::define('edit_products', function () {
            return true;
        });

        $this->actingAs($user);

        $response = $this->putJson("/api/products/{$product->id}", [
            'name' => 'Updated Game',
            'slug' => 'updated-game',
            'description' => 'Updated game description',
            'price' => 129.99,
            'stock' => 15,
            'category_id' => $newCategory->id,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Product updated successfully',
            ]);

        // Check database for updated values
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Updated Game',
            'slug' => 'updated-game',
            'description' => 'Updated game description',
            'price' => 129.99,
            'stock' => 15,
            'category_id' => $newCategory->id,
        ]);
    }

    #[Test]
    public function product_is_updated_with_new_images()
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();

        // Create product with initial images
        $product = Product::factory()->create([
            'category_id' => $category->id
        ]);

        // Add initial images
        Image::create([
            'product_id' => $product->id,
            'image_url' => 'products/original1.jpg',
            'is_primary' => true,
        ]);

        Image::create([
            'product_id' => $product->id,
            'image_url' => 'products/original2.jpg',
            'is_primary' => false,
        ]);

        Gate::define('edit_products', function () {
            return true;
        });

        $this->actingAs($user);

        // Setup fake storage
        Storage::fake('public');
        $newImage1 = UploadedFile::fake()->image('new1.jpg');
        $newImage2 = UploadedFile::fake()->image('new2.jpg');

        $response = $this->putJson("/api/products/{$product->id}", [
            'name' => 'Updated Game',
            'slug' => 'updated-game',
            'description' => 'Updated description',
            'price' => 129.99,
            'stock' => 15,
            'category_id' => $category->id,
            'images' => [$newImage1, $newImage2],
            'primary_index' => 1, // Second image should be primary
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Product updated successfully',
            ]);

        // Check that old images were deleted and new ones created
        $updatedProduct = Product::find($product->id);
        $this->assertEquals(2, $updatedProduct->images()->count());

        // Verify the second image is primary
        $secondImage = $updatedProduct->images()->where('is_primary', true)->first();
        $this->assertNotNull($secondImage);

        // Verify files were stored
        foreach ($updatedProduct->images as $image) {
            Storage::disk('public')->assertExists($image->image_url);
        }
    }

    #[Test]
    public function unique_constraints_allow_current_product()
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();

        // Create two products
        $product1 = Product::factory()->create([
            'name' => 'First Product',
            'slug' => 'first-product',
            'category_id' => $category->id
        ]);

        $product2 = Product::factory()->create([
            'name' => 'Second Product',
            'slug' => 'second-product',
            'category_id' => $category->id
        ]);

        Gate::define('edit_products', function () {
            return true;
        });

        $this->actingAs($user);

        // Update product1 with its own name and slug (should work)
        $response = $this->putJson("/api/products/{$product1->id}", [
            'name' => 'First Product', // Same name as before
            'slug' => 'first-product', // Same slug as before
            'description' => 'Updated description',
            'price' => 129.99,
            'stock' => 15,
            'category_id' => $category->id,
        ]);

        $response->assertStatus(200);

        // Try to update product1 with product2's name (should fail)
        $response = $this->putJson("/api/products/{$product1->id}", [
            'name' => 'Second Product', // Name of product2
            'slug' => 'updated-slug',
            'description' => 'Updated description',
            'price' => 129.99,
            'stock' => 15,
            'category_id' => $category->id,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }

}
