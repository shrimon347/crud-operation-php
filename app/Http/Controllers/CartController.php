<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class CartController extends Controller
{
    public function addToCart(Request $request)
    {
      
        $productId = $request->input('product_id');

        $product = Product::find($productId);
        if (!$product) {
            return redirect()->route('home')->with('error', 'Product not found!');
        }

        $cart = session()->get('cart', []);

        if (isset($cart[$productId])) {
            $cart[$productId]['quantity']++;
        } else {
            $cart[$productId] = [
                'name' => $product->name,
                'quantity' => 1,
                'price' => $product->selling_price,
                'image' => $product->image,
                'total_price' => $product->selling_price,
            ];
        }

        $cart[$productId]['total_price'] = $cart[$productId]['quantity'] * $cart[$productId]['price'];

        session()->put('cart', $cart);

        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += $item['total_price'];
        }

        $taxRate = 1.5/100; // 10%
        $tax = $subtotal * $taxRate;

        $discountPercentage = session()->get('discount', 0); 
        $discountAmount = ($subtotal * $discountPercentage) / 100; 

        // Calculate grand total
        $grandTotal = $subtotal + $tax - $discountAmount;

        session()->put('cart_totals', [
            'subtotal' => $subtotal,
            'tax' => $tax,
            'discount_percentage' => $discountPercentage,
            'discount_amount' => $discountAmount,
            'grand_total' => $grandTotal,
        ]);
        // Redirect with success message
        return redirect()->route('home')->with('success', 'Product added to cart!');
    }

    public function updateQuantity(Request $request)
{
    $cart = session()->get('cart', []);

    if (isset($cart[$request->product_id])) {
        $pricePerItem = $cart[$request->product_id]['price'];

        if ($request->action === 'increase') {
            $cart[$request->product_id]['quantity']++;
        } elseif ($request->action === 'decrease' && $cart[$request->product_id]['quantity'] > 1) {
            $cart[$request->product_id]['quantity']--;
        }

        $cart[$request->product_id]['total_price'] = $cart[$request->product_id]['quantity'] * $pricePerItem;

        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += $item['total_price'];
        }

        $taxRate = 1.5 /100; 
        $tax = $subtotal * $taxRate;

        $discountPercentage = session()->get('discount', 0);
        $discountAmount = ($subtotal * $discountPercentage) / 100;

        // Calculate grand total
        $grandTotal = $subtotal + $tax - $discountAmount;

        // Update cart_totals in the session
        session()->put('cart_totals', [
            'subtotal' => $subtotal,
            'tax' => $tax,
            'discount_percentage' => $discountPercentage,
            'discount_amount' => $discountAmount,
            'grand_total' => $grandTotal,
        ]);

        // Save the updated cart back to the session
        session()->put('cart', $cart);

        // Redirect with success message
        return redirect()->back()->with('success', 'Product quantity updated successfully!');
    }

    // If the product is not found in the cart, redirect back with an error message
    return redirect()->back()->with('error', 'Product not found in the cart!');
}

    public function removeItem(Request $request)
    {
        $cart = session()->get('cart', []);

        if(isset($cart[$request->product_id])) {
            unset($cart[$request->product_id]);
        }
        
        if (empty($cart)) {
        // If cart is empty, remove session values for totals and discounts
        session()->forget(['cart', 'cart_totals', 'discount']);
        } else {
            session()->put('cart', $cart);
        }
        return redirect()->back()->with('success', 'Product removed successfully!');
    }

    public function applyDiscount(Request $request)
    {
        $validated = $request->validate([
            'discount_percentage' => 'required|numeric|min:0|max:100',
        ]);

        $cartTotals = session()->get('cart_totals', [
            'subtotal' => 0,
            'tax' => 0,
            'discount_percentage' => 0,
            'discount_amount' => 0,
            'grand_total' => 0,
        ]);

        $discountPercentage = $validated['discount_percentage'];
        session()->put('discount', $discountPercentage);

        $subtotal = $cartTotals['subtotal'];
        $discountAmount = ($subtotal * $discountPercentage) / 100;

        $taxRate = 1.5 / 100; // 1.5%
        $tax = $subtotal * $taxRate;
        $grandTotal = $subtotal + $tax - $discountAmount;

        session()->put('cart_totals', [
            'subtotal' => $subtotal,
            'tax' => $tax,
            'discount_percentage' => $discountPercentage,
            'discount_amount' => $discountAmount,
            'grand_total' => $grandTotal,
        ]);

        // Redirect back with success message
        return redirect()->back()->with('success', 'Discount applied successfully!');
    }
    
    public function processPayment(Request $request)
    {
        try {
            session()->forget('cart');
            session()->forget('cart_totals');
            session()->forget('discount');

            return redirect()->route('home')->with('success', 'Payment successful! Your cart has been cleared.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Payment failed. Please try again.');
        }
    }

}
