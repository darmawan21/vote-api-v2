<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\ProductCategory;
use Exception;
use Illuminate\Http\Request;

class ProductCategoryController extends Controller
{
    public function all(Request $request)
    {
        $id = $request->input('id');
        $limit = $request->input('limit', 6);
        $name = $request->input('name');
        $show_product = $request->input('show_product');

        if($id) {

            $category = ProductCategory::find($id);

            if($category) {
                return ResponseFormatter::success(
                    $category,
                    'Data kategori berhasil diambil'
                );
            } else {
                return ResponseFormatter::error(
                    null,
                    'Data kategori tidak ada',
                    404
                );
            }
        }

        $category = ProductCategory::query();

        if($name) {
            $category->where('name', 'like', '%' . $name . '%');
        }

        if($show_product) {
            $category->with('products');
        }

        return ResponseFormatter::success(
            $category->paginate(),
            'Data list kategori berhasil diambil'
        );
    }

    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'name' => ['required', 'string', 'max:255']
            ]);

            ProductCategory::create($validatedData);

            return ResponseFormatter::success(
                $validatedData,
                'Data produk kategori berhasil ditambahkan',
            );
        } catch (Exception $error) {
            return ResponseFormatter::error([
                'message' => 'Something went wrong',
                'error' => $error,
            ], 'Add Product Category Failed', 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $rules = [
                'name' => ['required', 'string', 'max:255'],
            ];
    
            $validatedData = $request->validate($rules);
    
            ProductCategory::where('id', $id)->update($validatedData);
    
            return ResponseFormatter::success([
                $validatedData,
                'Data produk kategori berhasil diupdate',
            ], 'Authenticated');
        } catch (Exception $error) {
            return ResponseFormatter::error([
                'message' => 'Something went wrong',
                'error' => $error,
            ], 'Update Product Category Failed', 500);
        }
    }

    public function delete(Request $request, $categoryId)
    {
        
        try {
            $category = ProductCategory::where('id', $categoryId)->get();
            ProductCategory::destroy($category->id);
            
            return ResponseFormatter::success([
                'Data produk kategori berhasil dihapus'
            ]);
        } catch (Exception $error) {
            return ResponseFormatter::error([
                'message' => 'Something went wrong',
                'error' => $error,
            ], 'Delete Product Category Failed', 500);
        }
    }
}
