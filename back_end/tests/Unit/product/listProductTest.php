<?php

namespace Tests\Unit\product;
use App\Models\Product;
use App\Models\User;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Gate;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
class listProductTest extends TestCase
{
    use RefreshDatabase, WithFaker;
    #[Test]
    public function user_without_permission_cannot_view_products()
    {
        $user = User::factory()->create();

        Gate::define('view_products', function () {
            return false;
        });

        $this->actingAs($user);

        $response = $this->getJson('/api/products');

        $response->assertStatus(403)
            ->assertJson([
                'message' => 'You do not have permission to view products'
            ]);
    }


    #[Test]
    public function user_with_permission_can_view_all_products()
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();

        $products = Product::factory()->count(3)->create([
            'category_id' => $category->id
        ]);

        Gate::define('view_products', function () {
            return true;
        });

        $this->actingAs($user);

        // // Mock the lowStockNotification method to avoid actual notifications in tests
        // $mockMethod = Mockery::mock('alias:App\Http\Controllers\Api\V1\Admin\ProductController');
        // $mockMethod->shouldReceive('lowStockNotification')
        //            ->andReturn(null);

        $response = $this->getJson('/api/products');

        $response->assertStatus(200)
        
            ->assertJson([
                'message' => 'Success'
            ])
            ->assertJsonStructure([
                'products_list',
                'message'
            ]);

        $this->assertCount(3, $response->json('products_list'));
    }
    #[Test]
    public function test_returns_empty_products_list_when_no_products_exist()
    {
        $user = User::factory()->create();

        Gate::define('view_products', function () {
            return true;
        });

        $this->actingAs($user);

        // // Mock lowStockNotification
        // $mockMethod = Mockery::mock('alias:App\Http\Controllers\Api\V1\Admin\ProductController');
        // $mockMethod->shouldReceive('lowStockNotification')
        //            ->andReturn(null);

        $response = $this->getJson('/api/products');

        $response->assertStatus(200)
            ->assertJson([
                'products_list' => [],
                'message' => 'Success'
            ]);

        $this->assertEmpty($response->json('products_list'));
    }



}
