<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index(){
        $payments = Payment::orderBy("created_at","desc")->paginate(10);
        return response()->json([
            "message" => "success",
            "payments" => $payments
        ]);
    }
}
