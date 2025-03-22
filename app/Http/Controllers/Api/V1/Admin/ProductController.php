<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Image;
use App\Models\Product;
use App\Models\User;
use App\Notifications\LowStockNotification;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{

    public function index()
    {

        if(!auth()->user()->can('view_products')){
            return response()->json([
                'message' => 'You do not have permission to view products'
            ], 403);
        }
        $this->lowStockNotification();

        $products = Product::all();

        return response()->json([
            'products_list' => $products,
            'message' => 'Success'
        ], 200);
    }


    public function store(Request $request)
    {

        if(!auth()->user()->can('create_products')){
            return response()->json([
                'message' => 'You do not have permission to create products'
            ], 403);
        }
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:products',
            'slug' => 'required|string|max:255|unique:products,slug',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'images' => 'required|array',
            'images.*' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'primary_index' => 'required|integer|min:0|',
        ]);
        try{
            DB::beginTransaction();

            $product = Product::create([
                'name' => $data['name'],
                'slug' => $data['slug'],
                'description' => $data['description'],
                'price' => $data['price'],
                'stock' => $data['stock'],
                'category_id' => $data['category_id'],
            ]);
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $index => $image) {
                    $path = $image->store('products', 'public');
                    $isPrimary = ($data['primary_index'] == $index);
                    Image::create([
                        'product_id' => $product->id,
                        'image_url' => $path,
                        'is_primary' => $isPrimary,
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'message' => 'Product created successfully',
                'product' => $product,
                'images' => $product->images,
            ], 201);

        }catch(\Exception $e){
            DB::rollback();
            return response()->json([
                'message' => 'Failed To crrate the Product',
                'error' => $e->getMessage(),
            ], 500);

        }
    }


    public function show($id)
    {

        if (!auth()->user()->can('view_products')){
            return response()->json([
                'message' => 'You do not have permission to view this product'
            ], 403);
        }

        $product = Product::find($id);

        if(!$product){
            return response()->json([
                'message' => 'selected product does not exist',
                'status' => 'error 404'
            ], 404);
        }

        return response()->json($product, 200);
    }


    public function update(Request $request, $id)
    {

        if(!auth()->user()->can('edit_products')){
            return response()->json([
                'message' => 'You do not have permission to edit this product'
            ], 403);
        }

        $product = Product::find($id);

        if(!$product){
            return response()->json([
                'message' => 'Selected product does not exist',
                'status' => 'error 404'
            ], 404);
        }

        $data = $request->validate([
            'name' => 'required|string|max:255|unique:products,name,'.$product->id,
            'slug' => 'required|string|max:255|unique:products,slug,'.$product->id,
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'images' => 'sometimes|array',
            'images.*' => 'sometimes|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'primary_index' => 'sometimes|required_with:images|integer|min:0',
        ]);

        try {
            DB::beginTransaction();

            $product->update([
                'name' => $data['name'],
                'slug' => $data['slug'],
                'description' => $data['description'],
                'price' => $data['price'],
                'stock' => $data['stock'],
                'category_id' => $data['category_id'],
            ]);


            if ($request->hasFile('images')) {

                $product->images()->delete();

                foreach ($request->file('images') as $index => $image) {
                    $path = $image->store('products', 'public');
                    $isPrimary = ($request->primary_index == $index);
                    dump($isPrimary);
                    Image::create([
                        'product_id' => $product->id,
                        'image_url' => $path,
                        'is_primary' => $isPrimary,
                    ]);
                }
            }
            DB::commit();

            return response()->json([
                'message' => 'Product updated successfully',
                'product' => $product->fresh(),
                'images' => $product->images,
            ], 200);

        } catch(\Exception $e) {
            DB::rollback();
            return response()->json([
                'message' => 'Failed to update the Product',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // Soft delete
    public function destroy($id)
    {
        if(!auth()->user()->can('delete_products')){
            return response()->json([
                'message' => 'You do not have permission to delete this product'
            ], 403);
        }
        $product = Product::find($id);
        if(!$product){
            return response()->json([
                'message' => 'Selected product does not exist',
                'status' => 'error 404'
            ], 404);
        }
        $product->delete();
        return response()->json([
            'message' => 'Product deleted successfully'
        ]);
    }
    // Hard delete
    public function forceDelete($id)
    {
        if(!auth()->user()->can('delete_products')){
            return response()->json([
                'message' => 'You do not have permission to permanently delete this product'
            ], 403);
        }

        try {
            DB::beginTransaction();
            $product = Product::withTrashed()->find($id);
            if(!$product){
                return response()->json([
                    'message' => 'Selected product does not exist',
                    'status' => 'error 404'
                ], 404);
            }
            $product->images()->delete();
            $product->forceDelete();
            DB::commit();
            return response()->json([
                'message' => 'Product permanently deleted successfully'
            ], 200);

        } catch(\Exception $e) {
            DB::rollback();
            return response()->json([
                'message' => 'Failed to permanently delete the product',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function restore($id)
    {

        $product = Product::withTrashed()->find($id);

        if(!$product){
            return response()->json([
                'message' => 'Selected product does not exist',
                'status' => 'error 404'
            ], 404);
        }

        if(!$product->trashed()){
            return response()->json([
                'message' => 'Product is not deleted',
                'status' => 'error 400'
            ], 400);
        }

        $product->restore();

        return response()->json([
            'message' => 'Product restored successfully',
            'product' => $product
        ], 200);
    }

    public function lowStockNotification()
    {

        $products = Product::where('stock', '<', 5)->get();

        if ($products->count() > 0) {

            $notifiable_admin = User::role('super_admin')->first();

            $notifiable_admin->notify(new LowStockNotification($products));

            return response()->json([
                'low_stock_products' => $products,
                'message' => 'Low stock notification email sent successfully'
            ], 200);
        }

        return response()->json([
            'message' => 'No products with low stock found'
        ], 200);
    }
}
