<?php

namespace App\Http\Controllers;

use App\Services\ProductStorage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ProductController extends ResponseController
{
    /**
     * Display a listing of the products.
     */
    public function index()
    {
        return view('products.index');
    }

    /**
     *  List all products as rendered HTML table.
     */
    public function list(ProductStorage $storage)
    {
        $products = collect($storage->all())
            ->sortByDesc('submitted_at')
            ->values();

        return view('products.table', compact('products'))->render();
    }


    /**
     * Store a newly created product in storage.
     */
    public function store(Request $request, ProductStorage $storage)
    {
        $validated = $request->validate([
            'name' => 'required',
            'quantity' => 'required|numeric|min:1',
            'price' => 'required|numeric|min:0',
        ]);

        try {
            $products = $storage->all();

            $products[] = [
                'id' => (string) Str::uuid(),
                'name' => $validated['name'],
                'quantity' => $validated['quantity'],
                'price' => $validated['price'],
                'submitted_at' => now()->toDateTimeString()
            ];

            $storage->save($products);

            return $this->success('Product added successfully', $products);
        } catch (ValidationException $e) {
            return $this->error('Validation failed', $e->errors(), 422);
        } catch (\Exception $e) {
            return $this->error('Something went wrong! please try again', [], 500);
        }
    }

    /**
     * Render the products table.
     */
    public function render(Request $request)
    {
        $products = collect($request->all())
            ->sortByDesc('submitted_at')
            ->values();

        return view('products.table', compact('products'));
    }

    /**
     * Update the specified product in storage.
     */
    public function update($id, Request $request, ProductStorage $storage)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'quantity' => 'required|integer|min:0',
                'price' => 'required|numeric|min:0',
            ]);

            $products = collect($storage->all());

            $updated = false;

            $products = $products->map(function ($product) use ($id, $validated, &$updated) {
                if ($product['id'] === $id) {
                    $product['name'] = $validated['name'];
                    $product['quantity'] = $validated['quantity'];
                    $product['price'] = $validated['price'];
                    $updated = true;
                }
                return $product;
            });

            if (!$updated) {
                return $this->error('Product not found', [], 404);
            }

            $storage->save($products->values()->toArray());

            return $this->success('Product updated successfully', $products->values());
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->error('Validation failed', $e->errors(), 422);
        } catch (\Exception $e) {
            return $this->error('Update failed', [], 500);
        }
    }

    /**
     * Remove the specified product from storage.
     */
    public function destroy($id, ProductStorage $storage)
    {
        try {

            $products = collect($storage->all());

            $filtered = $products->reject(function ($product) use ($id) {
                return $product['id'] === $id;
            })->values();

            if ($products->count() === $filtered->count()) {
                return $this->error('Product not found', [], 404);
            }

            $storage->save($filtered->toArray());

            return $this->success('Product deleted successfully', $filtered);
        } catch (\Exception $e) {
            return $this->error('Delete failed', [], 500);
        }
    }
}
