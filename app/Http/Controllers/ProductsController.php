<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductsController extends Controller
{
    // GET /products
    public function index()
    {
        return response()->json(Product::all(), 200);
    }

    // POST /products
    public function store(Request $request)
{
    $this->validate($request, [
        'name'        => 'required|string|max:255',
        'sku'         => 'required|string|unique:products,sku',
        'price'       => 'required|numeric|min:0',
        'stock'       => 'required|integer|min:0',
        'category'    => 'required|string|max:255',
        'description' => 'nullable|string',
        'status'      => 'required|in:active,inactive',
    ]);

    $product = Product::create($request->only([
        'name', 'sku', 'price', 'stock', 'category', 'description', 'status'
    ]));

    return response()->json($product, 201);
}

    // GET /products/{id}
    public function show($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        return response()->json($product, 200);
    }

    // PUT /products/{id}
    public function update(Request $request, $id)
{
    $product = Product::find($id);

    if (!$product) {
        return response()->json(['message' => 'Product not found'], 404);
    }

    $this->validate($request, [
        'name'        => 'sometimes|string|max:255',
        'sku'         => 'sometimes|string|unique:products,sku,' . $id,
        'price'       => 'sometimes|numeric|min:0',
        'stock'       => 'sometimes|integer|min:0',
        'category'    => 'sometimes|string|max:255',
        'description' => 'nullable|string',
        'status'      => 'sometimes|in:active,inactive',
    ]);

    $data = $request->json()->all() ?: $request->all();

    $product->update($data);

    return response()->json($product, 200);
}

    // DELETE /products/{id}
    public function destroy($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        $product->delete();

        return response()->json(['message' => 'Product deleted'], 200);
    }
}
