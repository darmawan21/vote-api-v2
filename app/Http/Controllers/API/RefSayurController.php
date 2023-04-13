<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\RefSayur;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RefSayurController extends Controller
{
    public function all(Request $request)
    {
        $id = $request->input('id');
        // $limit = $request->input('limit', 6);
        $name = $request->input('name');
        $show_product = $request->input('show_product');

        if($id) {

            $ref_sayur = RefSayur::find($id);

            if($ref_sayur) {
                return ResponseFormatter::success(
                    $ref_sayur,
                    'Data ref sayur berhasil diambil'
                );
            } else {
                return ResponseFormatter::error(
                    null,
                    'Data ref sayur tidak ada',
                    404
                );
            }
        }

        $ref_sayur = RefSayur::query();

        if($name) {
            $ref_sayur->where('name', 'like', '%' . $name . '%');
        }

        if($show_product) {
            $ref_sayur->with('products');
        }

        return ResponseFormatter::success(
            $ref_sayur->paginate(),
            'Data list kategori berhasil diambil'
        );
    }

    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'price' => ['required', 'integer'],
            ]);

            RefSayur::create($validatedData);

            return ResponseFormatter::success(
                $validatedData,
                'Data Ref Sayur berhasil ditambahkan',
            );
        } catch (Exception $error) {
            return ResponseFormatter::error([
                'message' => 'Something went wrong',
                'error' => $error
            ], 'Add Ref Sayur Failed', 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $rules = [
                'name' => ['required', 'string', 'max:255'],
            ];
    
            $validatedData = $request->validate($rules);
    
            RefSayur::where('id', $id)->update($validatedData);
    
            return ResponseFormatter::success([
                $validatedData,
                'Data ref sayur berhasil diupdate',
            ], 'Authenticated');
        } catch (Exception $error) {
            return ResponseFormatter::error([
                'message' => 'Something went wrong',
                'error' => $error,
            ], 'Update Ref Sayur Failed', 500);
        }
    }

    public function delete(Request $request, $ref_sayur_id)
    {
        try {
            DB::table('ref_sayurs')->where('id', $ref_sayur_id)->delete();
            
            return ResponseFormatter::success([
                'Data ref sayur berhasil dihapus'
            ]);
        } catch (Exception $error) {
            return ResponseFormatter::error([
                'message' => 'Something went wrong',
                'error' => $error,
            ], 'Delete Ref Sayur Failed', 500);
        }
    }
}
