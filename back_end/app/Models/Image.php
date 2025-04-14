<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    protected $table = "products_images";
    protected $fillable = [
        'product_id',
        'image_url',
        'is_primary'
    ];

    public function product(){
        return $this->belongsTo(Product::class);
    }


}
