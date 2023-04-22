<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ProductController extends Controller
{
    public function index()
    {
        $products = Cache::remember('products', 3600, function () {
            return Product::all();
        });


        return response()->json($products);
    }

    public function show(Product $product)
    {
        $product = Product::findOrFail($product->id);
        return response()->json($product);
    }

    public function store(ProductRequest $request)
    {
        $validatedData = $request->validated();
        $product = Product::create([
            'name' => $validatedData['name'],
            'description' => $validatedData['description'],
            'price' => $validatedData['price'],
            'user_id' =>
            auth()->user()->id,
        ]);

        Cache::forget('products');

        return response()->json($product);
    }

    public function update(ProductRequest $request, Product $product)
    {
        $this->authorize('update', $product);
        $validatedData = $request->validated();
        $product->update([
            'name' => $validatedData['name'],
            'description' => $validatedData['description'],
            'price' => $validatedData['price'],
        ]);

        Cache::forget('products');

        return response()->json($product);
    }

    public function destroy(Product $product)
    {
        $this->authorize('delete', $product);
        $product->delete();

        Cache::forget('products');

        return response()->json(['message' => 'Product deleted successfully']);
    }
}