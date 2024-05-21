<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Session;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::all();
        $cart = session()->get('cart', []);
        return view('index', compact('products', 'cart'));
    }

    public function addToCart(Request $request)
    {
        $product = Product::find($request->product_id);
        $cart = session()->get('cart', []);

        $cartKey = $product->id . '-' . $request->size . '-' . $request->color;

        if (isset($cart[$cartKey])) {
            $cart[$cartKey]['quantity']++;
        } else {
            $cart[$cartKey] = [
                "id" => $product->id,
                "name" => $product->name,
                "quantity" => 1,
                "price" => $product->price,
                "size" => $request->size,
                "color" => $request->color
            ];
        }

        session()->put('cart', $cart);
        return response()->json(['success' => 'Product added to cart']);
    }

    public function removeFromCart(Request $request)
    {
        $cartKey = $request->cart_key;

        $cart = session()->get('cart', []);

        if (isset($cart[$cartKey])) {
            unset($cart[$cartKey]);
            session()->put('cart', $cart);
            return response()->json(['success' => 'Item removed from cart']);
        }

        return response()->json(['error' => 'Item not found in cart']);
    }


    public function getCart()
    {
        $cart = session()->get('cart', []);
        return response()->json($cart);
    }

    public function checkout(Request $request)
    {
        // Validate the request data
        $request->validate([
            'customer_name' => 'required|string',
            'customer_address' => 'required|string',
            'phone_number' => 'required|string',
        ]);

        // Create a new order
        $order = new Order();
        $order->customer_name = $request->input('customer_name');
        $order->customer_address = $request->input('customer_address');
        $order->phone_number = $request->input('phone_number');
        $order->total_price = 0; // Initialize total price

        // Save the order
        $order->save();

        // Process cart items
        $cart = session()->get('cart', []);

        foreach ($cart as $cartKey => $item) {
            $product = Product::find($item['id']);

            // Create a new order item
            $orderItem = new OrderItem();
            $orderItem->order_id = $order->id;
            $orderItem->product_id = $product->id;
            $orderItem->quantity = $item['quantity'];
            $orderItem->price = $product->price;
            $orderItem->size = $item['size'];
            $orderItem->color = $item['color'];

            // Calculate and update total price
            $order->total_price += $orderItem->quantity * $orderItem->price;

            // Save the order item
            $orderItem->save();
        }

        // Update the total price of the order
        $order->save();

        // Clear the cart after successful checkout
        Session::forget('cart');

        // Redirect to a thank you page or any other appropriate action
        return redirect()->route('home')->with('success', 'Order placed successfully!');
    }
}
