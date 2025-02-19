<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        // Get products filtered by type, if 'type' is provided
        $type = $request->input('type');

        if ($type) {
            $products = Product::where('type', $type)->get();
        } else {
            $products = Product::all();
        }
        return view('products', compact('products','type'));
    }
    public function store(Request $request)
    {

        // Validate the input data
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
            'type' => 'required|string',
            'purchase_price' => 'required|numeric',
            'selling_price' => 'required|numeric',
            'total_sold_quantity' => 'required|integer',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('images', 'public');
        }

        $product = new Product;
        $product->name = $validated['name'];
        $product->image = $imagePath;
        $product->type = $validated['type'];
        $product->purchase_price = $validated['purchase_price'];
        $product->selling_price = $validated['selling_price'];
        $product->total_sold_quantity = $validated['total_sold_quantity'];
        $product->save();


        return redirect()->back()->with('success', 'Product added successfully!');
    }

     public function edit($id)
     {
         $product = Product::findOrFail($id); 
         return view('edit', compact('product'));
     }
 
 
     public function update(Request $request, $id)
     {
         // Validate the input
         $validated = $request->validate([
             'name' => 'required|string|max:255',
             'image' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
             'type' => 'required|string',
             'purchase_price' => 'required|numeric',
             'selling_price' => 'required|numeric',
             'total_sold_quantity' => 'required|integer',
         ]);
 
         $product = Product::findOrFail($id); 
         // Update product details
         $product->name = $request->name;
         $product->type = $request->type;
         $product->purchase_price = $request->purchase_price;
         $product->selling_price = $request->selling_price;
         $product->total_sold_quantity = $request->total_sold_quantity;
 
         if ($request->hasFile('image')) {
             $imagePath = $request->file('image')->store('images', 'public');
             $product->image = $imagePath;
         }

         $product->save();
 
         return redirect()->route('home')->with('success', 'Product updated successfully!');
     }

     public function destroy($id)
    {
        $product = Product::findOrFail($id); 

        if ($product->image) {
            \Storage::delete('public/' . $product->image);
        }
        $product->delete();

        return redirect()->route('product.index')->with('success', 'Product deleted successfully!');
    }

    
    public function search(Request $request)
    {
        $query = $request->input('query');

        $products = Product::where('name', 'like', '%' . $query . '%')
                            ->orWhere('type', 'like', '%' . $query . '%')
                            ->get();

        // Pass the results to the view
        return view('products', compact('products', 'query'));
    }
}
