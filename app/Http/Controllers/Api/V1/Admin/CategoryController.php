<?php

namespace App\Http\Controllers\api\v1\admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(){
        if (!auth()->user()->hasRole('super_admin')) {
            return response()->json([
                'message' => 'You do not have permission to view categories'
            ], 403);
        }
        $categories = Category::all();
        return response()->json([
            'message' => 'Success',
            'data' => $categories
        ], 200);
    }
    public function store(Request $request){
        if (!auth()->user()->hasRole('super_admin')) {
            return response()->json([
                'message' => 'You do not have permission to create categories'
            ], 403);
        }
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:categories',
            'slug' => 'required|string|max:255|unique:categories,slug',
        ]);
        $category = Category::create($data);
        return response()->json([
            'message' => 'Category created successfully',
            'data' => $category
        ], 201);
    }
    public function update(Request $request, $id)
    {
        if (!auth()->user()->hasRole('super_admin')) {
            return response()->json([
                'message' => 'You do not have permission to update categories'
            ], 403);
        }

        $category = Category::find($id);

        if (!$category) {
            return response()->json([
                'message' => 'Category not found',
                'status' => 'error 404'
            ], 404);
        }

        $data = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,'.$category->id,
            'slug' => 'required|string|max:255|unique:categories,slug,'.$category->id,
        ]);

        $category->update($data);

        return response()->json([
            'message' => 'Category updated successfully',
            'data' => $category
        ], 200);
    }

    public function destroy($id)
    {
        if (!auth()->user()->can('view_categories')) {
            return response()->json([
                'message' => 'You do not have permission to delete categories'
            ], 403);
        }

        $category = Category::find($id);

        if (!$category) {
            return response()->json([
                'message' => 'Category not found',
                'status' => 'error 404'
            ], 404);
        }


        if ($category->products()->count() > 0) {
            return response()->json([
                'message' => 'Cannot delete category with associated products',
                'status' => 'error 400'
            ], 400);
        }

        $category->delete();

        return response()->json([
            'message' => 'Category deleted successfully'
        ], 200);
    }
}
