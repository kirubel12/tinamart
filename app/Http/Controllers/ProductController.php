<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProductController extends Controller
{
    protected $fillable = [
        'name',
        'slug',
        'sku',
        'stock',
        'description',
        'price',
        'category_id',
        'image_url'
    ];
}
