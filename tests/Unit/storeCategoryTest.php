<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class StoreCategoryTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    #[Test]
    public function super_admin_can_create_category()
    {

        $user = User::factory()->create();

        $user->shouldReceive('hasRole')
            ->with('super_admin')
            ->andReturn(true);

        $this->actingAs($user);

        $categoryData = [
            'name' => 'Test Category',
            'slug' => 'test-category',
        ];

        $response = $this->postJson('/api/v1/admin/categories', $categoryData);

        $response->assertStatus(201)
            ->assertJsonFragment([
                'message' => 'Category created successfully',
                'data' => [
                    'name' => 'Test Category',
                    'slug' => 'test-category',
                ]
            ]);

        $this->assertDatabaseHas('categories', [
            'name' => 'Test Category',
            'slug' => 'test-category',
        ]);
    }

   #[Test]
    public function non_super_admin_cannot_create_category()
    {

        $user = User::factory()->create();


        $user->shouldReceive('hasRole')
            ->with('super_admin')
            ->andReturn(false);

        $this->actingAs($user);

        $categoryData = [
            'name' => 'Test Category',
            'slug' => 'test-category',
        ];

        $response = $this->postJson('/api/v1/admin/categories', $categoryData);

        $response->assertStatus(403)
            ->assertJson([
                'message' => 'You do not have permission to create categories'
            ]);

        $this->assertDatabaseMissing('categories', [
            'name' => 'Test Category',
        ]);
    }

   #[Test]
    public function it_validates_required_fields()
    {

        $user = User::factory()->create();
        $user->shouldReceive('hasRole')
            ->with('super_admin')
            ->andReturn(true);

        $this->actingAs($user);

        $response = $this->postJson('/api/v1/admin/categories', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'slug']);
    }

   #[Test]
    public function it_validates_unique_category_name_and_slug()
    {
        // Create existing category
        Category::create([
            'name' => 'Existing Category',
            'slug' => 'existing-category'
        ]);

        // Create super admin user
        $user = User::factory()->create();
        $user->shouldReceive('hasRole')
            ->with('super_admin')
            ->andReturn(true);

        $this->actingAs($user);

        $categoryData = [
            'name' => 'Existing Category',
            'slug' => 'existing-category',
        ];

        $response = $this->postJson('/api/v1/admin/categories', $categoryData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'slug']);
    }
}
