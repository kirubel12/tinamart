<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::all();

        if ($categories->isEmpty()) {
            return response()->json(['message' => 'No categories found'], 404);
        }
        return response()->json([
            'data' => $categories,
            'message' => 'Categories retrieved successfully',
            'status' => 'success'
        ], 201);

    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:categories,slug',
        ]);
        try {
            $category = Category::create([
                'user_id' => auth()->id(),
                'name' => $request->name,
                'slug' => $request->slug,
            ]);

            return response()->json([
                'data' => $category,
                'message' => 'Category created successfully',
                'status' => 'success'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error creating category: ' . $e->getMessage(),
                'status' => 'error'
            ], 500);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json([
                'message' => 'An unexpected error occurred: ' . $th->getMessage(),
                'status' => 'error'
            ], 500);
        }
    }
}
