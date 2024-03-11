<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function index()
    {
        $product = Product::with('user')->where(['user_id' => Auth::user()->id])->OrderBy('id', "desc")->paginate(10)->toArray();

        if (Auth::user()->role === 'admin') {
            $product = Product::OrderBy("id", "DESC")->paginate(2)->toArray();
        }else if (Auth::user()->role === 'editor') {
            $product = Product::where(['user_id' => Auth::user()->id])->OrderBy("id", "DESC")->paginate(2)->toArray();
        }else{
            $product = Product::OrderBy("id", "DESC")->paginate(10)->toArray();
        }

        $response = [
            "total_count" => $product["total"],
            "limit" => $product["per_page"],
            "pagination" => [
                "next_page" => $product["next_page_url"],
                "current_page" => $product["current_page"]
            ],
            "data" => $product["data"],
        ];

        return response()->json($response, 200);
    }

    public function store(Request $request)
    {
        $input = $request->all();

        if (Gate::denies('store')) {
            return response()->json([
                'succes' => false,
                'status' => 403,
                'message' => 'You are unauthorixed'
            ], 200);
        }

        $validationRules = [
            'name' => 'required|min:2',
            'price' => 'required|min:2',
        ];

        $validator = Validator::make($input, $validationRules);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $product = Product::where('user_id', Auth::user()->id)->first();

        if (!$product) {
            $product = new Product;
            $product->user_id = Auth::user()->id;
        }

        $product->name = $request->input('name');
        $product->price = $request->input('price');

        if ($request->hasFile('image')) {
            $nameReplace = str_replace(' ', '_', $request->input('name'));

            $imageName = Auth::user()->id . '_' . $nameReplace;
            $request->file('image')->move(storage_path('uploads/image_product'), $imageName);

            $current_image_path = storage_path('avatar') . '/' . $product->image;
            if (file_exists($current_image_path)) {
                unlink($current_image_path);
            }

            $product->image = $imageName;
        }

        if ($request->hasFile('video')) {
            $titleReplace = str_replace(' ', '_', $request->input('title'));
            // $lastName = str_replace(' ', '_', $request->input('last_name'));

            $videoName = Auth::user()->id . '_' . $titleReplace;
            $request->file('video')->move(storage_path('uploads/product_video'), $videoName);

            if (!empty($product->video)) {
                $current_video_path = storage_path('uploads/product_video') . '/' . $product->video;
                if (file_exists($current_video_path)) {
                    unlink($current_video_path);
                }
            }
            $product->video = $videoName;
        }
        $product->save();

        return response()->json($product, 200);
    }

    public function show($id)
    {
        $product = Product::with(['user' => function($query){
            $query->select('id', 'name');
        }])->find($id);

        if (!$product) {
            abort(404);
        }

        return response()->json($product, 200);
    }

    public function update(Request $request, $id)
    {
        $input = $request->all();

        if (Gate::denies('store')) {
            return response()->json([
                'succes' => false,
                'status' => 403,
                'message' => 'You are unauthorixed'
            ], 200);
        }

        $validationRules = [
            'name' => 'required|min:2',
            'price' => 'required|min:2',
            'user_id' => 'required|min:1',
        ];

        $validator = Validator::make($input, $validationRules);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $product = Product::where('user_id', Auth::user()->id)->first();

        if (!$product) {
            $product = new Product;
            $product->user_id = Auth::user()->id;
        }

        $product->name = $request->input('name');
        $product->price = $request->input('price');

        if ($request->hasFile('image')) {
            $nameReplace = str_replace(' ', '_', $request->input('name'));

            $imageName = Auth::user()->id . '_' . $nameReplace;
            $request->file('image')->move(storage_path('uploads/image_product'), $imageName);

            $current_image_path = storage_path('avatar') . '/' . $product->image;
            if (file_exists($current_image_path)) {
                unlink($current_image_path);
            }

            $product->image = $imageName;
        }

        if ($request->hasFile('video')) {
            $titleReplace = str_replace(' ', '_', $request->input('title'));
            // $lastName = str_replace(' ', '_', $request->input('last_name'));

            $videoName = Auth::user()->id . '_' . $titleReplace;
            $request->file('video')->move(storage_path('uploads/product_video'), $videoName);

            if (!empty($product->video)) {
                $current_video_path = storage_path('uploads/product_video') . '/' . $product->video;
                if (file_exists($current_video_path)) {
                    unlink($current_video_path);
                }
            }
            $product->video = $videoName;
        }

        $product->fill($input);
        $product->save();

        return response()->json($product, 200);
    }

    public function destroy($id)
    {
        $product = Product::find($id);

        if (!$product) {
            abort(404);
        }

        if (Gate::denies('update', $product)) {
            return response()->json([
                'succes' => false,
                'status' => 403,
                'message' => 'You are unauthorixed'
            ], 200);
        }

        $product->delete();
        $message = ['message' => 'Deketed Successfully', 'product_id' => $id];

        return response()->json($message, 200);
    }

    public function image($imageName)
    {
        $imagePath = storage_path('uploads/image_product'). '/' . $imageName;
        if (file_exists($imagePath)) {
            $file = file_get_contents($imagePath);
            return response($file, 200)->header('Content-Type', 'image/jpeg');
        }
        return response()->json(array(
            "message" => "Image not found"
        ), 401);
    }

    public function video($videoName)
    {
        $videoPath = storage_path('uploads/product_video'). '/' . $videoName;
        if (file_exists($videoPath)) {
            $file = file_get_contents($videoPath);
            return response($file, 200)->header('Content-Type', 'video/mp4');
        }
        return response()->json(array(
            "message" => "Video not found"
        ), 401);
    }
}

