<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\User;
use App\Models\Category;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(){

        if(!auth()->user()->can('view_dashboard')){
            return response()->json([
                'message' => 'You are not authorized to view this page',
                'status' => 'error'
            ], 403);
        }

        $general_stats = [
            'total_users' => User::count(),
            'total_products' => Product::count(),
            'total_low_products_in_stock' => Product::where('stock', '<', 10)->count()
        ];

        return response()->json([
            'data' => $general_stats,
            'message' => 'Dashboard data retrieved successfully',
            'status' => 'success'
        ], 200);
    }
}
