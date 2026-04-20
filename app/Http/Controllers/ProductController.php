<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller 
{
    /**
     * Show All Categories
     */

    public function indexCategory(){
        $categories = Category::all();

        return response()->json([
            'result' => ['status' => "Success 200", "message" => "All Category Success Retrieved"],
            'data' => $categories
        ], 200);
    }

    /**
     * Store Category
     */

    public function storeCategory(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|unique:categories,name|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['result' => ['status' => 'Error 422', 'message' => $validator->errors()->first()]], 422);
        }
        $category = Category::create($request->only('name'));

        return response()->json([
            'result' => ['status' => 'Success 201', 'message' => 'Category created successfully'],
            'data' => $category
        ], 201);
        
    }

    /**
     * Delete Category
     */

    public function destroyCategory($id){
        $category = Category::findOrFail($id);
        if ($category->products()->count() > 0) {
            return response()->json([
                'result' => ['status' => 'Error 400', 'message' => 'Cannot delete category that has products attached']
            ], 400);
        }
        $category->delete();
        return response()->json(['result' => ['status' => 'Success 200', 'message' => 'Category deleted']], 200);
    }

    /**
     * Show all products
     */

     public function indexProducts(Request $request)
    {
        $query = Product::with('category');

        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $products = $query->latest()->get();

        $formattedData = $products->map(function ($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'price' => (float) $product->price,
                'category' => [
                    'id' => $product->category->id,
                    'name' => $product->category->name
                ],
                'product_features' => [
                    'tags' => $product->tags,
                    'ingredients' => $product->ingredients,
                    'nutrition' => $product->nutrition
                ]
            ];
        });

        return response()->json([
            'result' => ['status' => 'Success 200', 'message' => 'Products retrieved'],
            'data' => $formattedData
        ], 200);
    }

    /**
     * Store new record products
     */

    public function storeProduct(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'tags' => 'nullable|string',
            'ingredients' => 'nullable|string',
            'nutrition' => 'nullable|array'
        ]);

        if ($validator->fails()) {
            return response()->json(['result' => ['status' => 'Error 422', 'message' => $validator->errors()->first()]], 422);
        }

        $product = Product::create($request->all());

         return response()->json([
            'result' => ['status' => 'Success 201', 'message' => "Product {$product->name} created successfully"],
            'data' => $product->load('category')
        ], 201);

    }

    /**
     * Show Specifik product
     */

    public function showProduct($id)
    {
        $product = Product::with('category')->findOrFail($id);

        return response()->json([
            'result' => ['status' => 'Success 200', 'message' => 'Product details retrieved'],
            'data' => [
                'id' => $product->id,
                'name' => $product->name,
                'price' => (float) $product->price,
                'category' => $product->category,
                'product_features' => [
                    'tags' => $product->tags,
                    'ingredients' => $product->ingredients,
                    'nutrition' => $product->nutrition
                ]
            ]
        ], 200);
    }

    /**
     * Update record product
     */

    public function updateProduct(Request $request, $id){
        $product = Product::findOrFail($id);
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'price' => 'sometimes|numeric|min:0',
            'category_id' => 'sometimes|exists:categories,id',
            'tags' => 'nullable|string',
            'ingredients' => 'nullable|string',
            'nutrition' => 'nullable|array'
        ]);

        if ($validator->fails()) {
            return response()->json(['result' => ['status' => 'Error 422', 'message' => $validator->errors()->first()]], 422);
        }

        $product->update($request->all());

        return response()->json([
            'result' => ['status' => 'Success 200', 'message' => "Product {$product->name} updated"],
            'data' => $product->load('category')
        ], 200);
    }

    /**
     * Destory Product
     */

    public function destroyProduct($id){
        $product = Product::findOrFail($id);
        $product->delete();
        return response()->json(['result' => ['status' => 'Success 200', 'message' => 'Product deleted']], 200);
    }
}