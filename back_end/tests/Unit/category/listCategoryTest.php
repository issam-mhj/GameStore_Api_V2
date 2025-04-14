<?php

namespace Tests\Unit\category;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Mockery;
use Illuminate\Support\Facades\Gate;
use PHPUnit\Framework\Attributes\Test;

class listCategoryTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function super_admin_can_view_categories()
    {
        Category::factory()->count(3)->create();

        $admin = User::factory()->create();
        Gate::define('view_categories', function () {
            return true;
        });
        $this->actingAs($admin);

        // Make request to the index endpoint
        $response = $this->getJson('/api/v1/admin/categories');
        dd($response);

        // Assert successful response
        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'data'
            ])
            ->assertJson([
                'message' => 'Success'
            ]);

        // Assert that we received 3 categories
        $this->assertCount(3, $response->json('data'));
    }

    #[Test]
    public function non_super_admin_cannot_view_categories()
    {
        // Create regular user without super_admin role
        $user = User::factory()->create();

        // Mock the hasRole method to return false for super_admin
        $user = Mockery::mock($user)->makePartial();
        $user->shouldReceive('hasRole')
            ->with('super_admin')
            ->andReturn(false);

        $this->actingAs($user);

        // Make request to the index endpoint
        $response = $this->getJson('/api/v1/admin/categories');

        // Assert forbidden response
        $response->assertStatus(403)
            ->assertJson([
                'message' => 'You do not have permission to view categories'
            ]);
    }

    #[Test]
    public function index_returns_all_categories()
    {
        // Create categories with known values
        $category1 = Category::factory()->create(['name' => 'Action Games']);
        $category2 = Category::factory()->create(['name' => 'Strategy Games']);
        $category3 = Category::factory()->create(['name' => 'RPG Games']);

        // Create super_admin user
        $admin = User::factory()->create();
        $admin = Mockery::mock($admin)->makePartial();
        $admin->shouldReceive('hasRole')
            ->with('super_admin')
            ->andReturn(true);

        $this->actingAs($admin);

        // Make request to the index endpoint
        $response = $this->getJson('/api/v1/admin/categories');

        // Assert response contains all categories
        $response->assertStatus(200);
        $data = $response->json('data');

        $this->assertCount(3, $data);
        $categoryNames = collect($data)->pluck('name')->toArray();
        $this->assertContains('Action Games', $categoryNames);
        $this->assertContains('Strategy Games', $categoryNames);
        $this->assertContains('RPG Games', $categoryNames);
    }
}
