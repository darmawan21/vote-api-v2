<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Vote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function all(Request $request)
    {
        $id = $request->input('id');
        $limit = $request->input('limit', 6);
        $name = $request->input('name');
        $description = $request->input('description');
        $tags = $request->input('tags');
        $categories = $request->input('categories');

        $price_from = $request->input('price_from');
        $price_to = $request->input('price_to');

        if($id)
        {
            $product = Product::with(['category', 'galleries'])->find($id);

            if($product) {
                return ResponseFormatter::success(
                    $product,
                    'Data produk berhasil diambil'
                );
            } else {
                return ResponseFormatter::error(
                    null,
                    'Data produk tidak ada',
                    404
                );
            }
        }

        // $product = Product::with(['category', 'galleries', 'votes' => function ($query) {
        //     $query->orderBy('score', 'desc');
        // }]);
        $product = Product::with(['category', 'galleries'])->orderBy(
            DB::raw("(SELECT sum(score) FROM votes WHERE product_id = products.id)")
            , 'desc');
        
        // $top_product = Product::with(['category', 'galleries', 'votes']);

        if($name) {
            $product->where('name', 'like', '%' . $name . '%');
        }
        
        if($description) {
            $product->where('description', 'like', '%' . $description . '%');
        }

        if($tags) {
            $product->where('tags', 'like', '%' . $tags . '%');
        }

        if($price_from) {
            $product->where('price_from', '>=', $price_from);
        }

        if($price_to) {
            $product->where('price_to', '<=', $price_to);
        }

        if($categories) {
            $product->where('categories_id', $categories);
        }

        return ResponseFormatter::success(
            $product->paginate($limit),
            'Data produk berhasil diambil'
        );

    }

    public function vote(Request $request, $productId)
    {
        $product = Product::find($productId);

        if (!$product) {
            return ResponseFormatter::error(
                null,
                'Product not found',
                404
            );
        }

        $user = Auth::user();  
        
        if ($product->votes()->where('user_id', $user->id)->exists()) {
            return ResponseFormatter::error(
                'You have already voted for this product',
                400
            );
        }

        $vote = new Vote();
        $vote->score = $request->input('score');
        $vote->product_id = $product->id;
        $vote->user_id = $user->id;
        $vote->save();

        return ResponseFormatter::success(
            $vote->score,
            'Voted recorded',
        );
    }
}
