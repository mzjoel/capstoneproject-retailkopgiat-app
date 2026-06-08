<?php

namespace App\Modules\Catalog\Controllers;

use App\Http\Controllers\Controller; 
use App\Modules\Catalog\Models\Product;
use App\Modules\Catalog\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;

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
                'image' => $product->image_url,
                'is_available' => (bool) $product->is_available,
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
                'image' => $product->image_url,
                'is_available' => (bool) $product->is_available,
                'product_features' => [
                    'tags' => $product->tags,
                    'ingredients' => $product->ingredients,
                    'nutrition' => $product->nutrition
                ]
            ]
        ], 200);
    }

    /**
     * Show Product Detail Page (Inertia)
     */
    public function showProductPage($id)
    {
        $product = Product::with('category')->findOrFail($id);

        return Inertia::render('Catalog/DetailProducts', [
            'product' => [
                'id' => $product->id,
                'name' => $product->name,
                'description' => $product->description,
                'price' => 'Rp ' . number_format($product->price, 0, ',', '.'),
                'raw_price' => (float) $product->price,
                'category' => $product->category->name,
                'rating' => '4.8', // Placeholder for now
                'stock' => 24,    // Placeholder for now
                'contextBadge' => 'Menu Pilihan', // Placeholder
                'image' => $product->image_url ?: 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&w=800&q=80',
                'is_available' => (bool) $product->is_available,
                'thumbnails' => [
                    $product->image_url ?: 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1504674900247-0877df9cc836?auto=format&fit=crop&w=800&q=80'
                ],
                'meta' => [
                    ['icon' => 'schedule', 'label' => 'Estimasi', 'value' => '15-20 Menit'],
                    ['icon' => 'local_fire_department', 'label' => 'Energi', 'value' => '540 Kcal'],
                ],
                'product_features' => [
                    'tags' => $product->tags,
                    'ingredients' => $product->ingredients,
                    'nutrition' => $product->nutrition
                ]
            ]
        ]);
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

    public function showWishList(){
        return Inertia::render('Catalog/Favorite');
    }
}