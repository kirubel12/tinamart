<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::all();

        if ($products->isEmpty()) {
            return response()->json(['message' => 'No products found'], 404);
        }
        return response()->json([
            'message' => 'Products retrieved successfully',
            'data' => $products,

        ], 200);

    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'slug' => 'required|string|max:255|unique:products,slug',
                'sku' => 'required|string|max:255|unique:products,sku',
                'stock' => 'required|integer|min:0',
                'description' => 'nullable|string',
                'price' => 'required|numeric|min:0',
                'category_id' => 'required|exists:categories,id',
                'image_url' => 'nullable|url'
            ]);

            $product = Product::create([
                'name' => $validated['name'],
                'slug' => $validated['slug'],
                'sku' => $validated['sku'],
                'stock' => $validated['stock'],
                'description' => $validated['description'],
                'price' => $validated['price'],
                'category_id' => $validated['category_id'],
                'image_url' => $validated['image_url'] ?? null,
                'user_id' => auth()->id(),
            ]);

            return response()->json([
                'message' => 'Product created successfully',
                'data' => $product,
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Failed to create product',
                'error' => $e->getMessage(),
            ], 500);
        }

    }

    public function update(Request $request, $id)
    {
        try {
            $product = Product::findOrFail($id);

            $validated = $request->validate([
                'name' => 'sometimes|required|string|max:255',
                'slug' => 'sometimes|required|string|max:255|unique:products,slug,' . $product->id,
                'sku' => 'sometimes|required|string|max:255|unique:products,sku,' . $product->id,
                'stock' => 'sometimes|required|integer|min:0',
                'description' => 'nullable|string',
                'price' => 'sometimes|required|numeric|min:0',
                'category_id' => 'sometimes|required|exists:categories,id',
                'image_url' => 'nullable|url'
            ]);

            $product->update($validated);

            return response()->json([
                'message' => 'Product updated successfully',
                'data' => $product,
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Failed to update product',
                'error' => $e->getMessage(),
            ], 500);
        }

    }

    public function show($id)
    {
        try {
            $product = Product::findOrFail($id);
            return response()->json([
                'message' => 'Product retrieved successfully',
                'data' => $product,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Product not found',
                'error' => $e->getMessage(),
            ], 404);
        }
    }
    public function destroy($id)
    {
        try {
            $product = Product::findOrFail($id);
            $product->delete();
            return response()->json([
                'message' => 'Product deleted successfully',
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Failed to delete product',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getProductsByCategory($categoryId)
    {
        try {
            $products = Product::where('category_id', $categoryId)->get();

            if ($products->isEmpty()) {
                return response()->json(['message' => 'No products found for this category'], 404);
            }

            return response()->json([
                'message' => 'Products retrieved successfully',
                'data' => $products,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve products',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
