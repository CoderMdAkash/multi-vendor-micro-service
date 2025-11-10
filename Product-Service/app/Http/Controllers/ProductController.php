<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index() 
    {
        $product = Product::all();

        return response()->json([
            'success' => true,
            'message' => 'Products retrieved successfully',
            'data' => ProductResource::collection($product)
        ]);
    }

    public function store(Request $request)
    {
        $validate = $request->validate([
            'name' => 'required|unique:products|max:255',
            'description' => 'required|max:1200',
            'price' => 'required|numeric|min:0.01|max:123456789.99',
        ]);

        $validate['user_id'] = @$request->user['id'];

        try {
            $product = Product::create($validate);
        } catch (\Throwable $th) {
            return [
                'success' => false,
                'message' => $th->getMessage()
            ];
        }

        return [
            'success' => true,
            'message' => 'Product created successfully',
            'data' => new ProductResource($product)
        ];
    }

    public function update(Request $request, $id) 
    {
        $product = Product::find($id);
        
        if (!$product) {
            return [
                'success' => false,
                'message' => 'Product not found'
            ];
        }

        $validate = $request->validate([
            'name' => 'required|max:255|unique:products,name,' . $product->id,
            'description' => 'required|max:1200',
            'price' => 'required|numeric|min:0.01|max:123456789.99',
        ]);

        $validate['user_id'] = @$request->user['id'];


        try {
            $product->update($validate);
        } catch (\Throwable $th) {
            return [
                'success' => false,
                'message' => $th->getMessage()
            ];
        }

        return [
            'success' => true,
            'message' => 'Product updated successfully',
            'data' => new ProductResource($product)
        ];
    }

    public function destroy($id) 
    {
        $product = Product::find($id);
        if (!$product) {
            return [
                'success' => false,
                'message' => 'Product not found'
            ];
        }

        try {
            $product->delete();
        } catch (\Throwable $th) {
            return [
                'success' => false,
                'message' => $th->getMessage()
            ];
        }

        return [
            'success' => true,
            'message' => 'Product deleted successfully'
        ];
    }
}
