<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index()
    {
        $order = Order::with('user','product')->where(['user_id' => Auth::user()->id])->OrderBy('id', "desc")->paginate(10)->toArray();

        $response = [
            "total_count" => $order["total"],
            "limit" => $order["per_page"],
            "pagination" => [
                "next_page" => $order["next_page_url"],
                "current_page" => $order["current_page"]
            ],
            "data" => $order["data"],
        ];

        return response()->json($response, 200);
    }

    public function store(Request $request)
    {
        $product = Product::find($request->input('product_id'));
        $input = [
            'user_id' => Auth::user()->id,
            'product_id' => $request->input('product_id'),
            'qyt' => $request->input('qyt'),
            'pay' => $request->input('qyt') * $product->price
        ];

        $order = Order::create($input);

        return response()->json($order, 200);
    }

    public function show($id)
    {
        $order = Order::with('product')->find($id);

        if (!$order) {
            abort(404);
        }

        return response()->json($order, 200);
    }

    public function update(Request $request, $id)
    {
        $input = $request->all();

        $order = Order::where(['user_id' => Auth::user()->id])->find($id);

        if (!$order) {
            abort(404);
        }


        $order->fill($input);
        $order->save();

        return response()->json($order, 200);
    }

    public function destroy($id)
    {
        $order = Order::where(['user_id' => Auth::user()->id])->find($id);

        if (!$order) {
            abort(404);
        }

        $order->delete();
        $message = ['message' => 'deleted Successfully', 'order_id' => $id];

        return response()->json($message, 200);
    }
}
