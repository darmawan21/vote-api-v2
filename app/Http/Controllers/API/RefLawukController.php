<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\RefLawuk;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RefLawukController extends Controller
{
    public function all(Request $request)
    {
        $id = $request->input('id');
        // $limit = $request->input('limit', 6);
        $name = $request->input('name');
        $show_product = $request->input('show_product');

        if($id) {

            $ref_lawuk = RefLawuk::find($id);

            if($ref_lawuk) {
                return ResponseFormatter::success(
                    $ref_lawuk,
                    'Data ref lawuk berhasil diambil'
                );
            } else {
                return ResponseFormatter::error(
                    null,
                    'Data ref lawuk tidak ada',
                    404
                );
            }
        }

        $ref_lawuk = RefLawuk::query();

        if($name) {
            $ref_lawuk->where('name', 'like', '%' . $name . '%');
        }

        if($show_product) {
            $ref_lawuk->with('products');
        }

        return ResponseFormatter::success(
            $ref_lawuk->paginate(),
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

            RefLawuk::create($validatedData);

            return ResponseFormatter::success(
                $validatedData,
                'Data Ref lawuk berhasil ditambahkan',
            );
        } catch (Exception $error) {
            return ResponseFormatter::error([
                'message' => 'Something went wrong',
                'error' => $error
            ], 'Add Ref lawuk Failed', 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $rules = [
                'name' => ['required', 'string', 'max:255'],
            ];
    
            $validatedData = $request->validate($rules);
    
            RefLawuk::where('id', $id)->update($validatedData);
    
            return ResponseFormatter::success([
                $validatedData,
                'Data ref lawuk berhasil diupdate',
            ], 'Authenticated');
        } catch (Exception $error) {
            return ResponseFormatter::error([
                'message' => 'Something went wrong',
                'error' => $error,
            ], 'Update Ref lawuk Failed', 500);
        }
    }

    public function delete(Request $request, $ref_lawuk_id)
    {
        try {
            DB::table('ref_lawuks')->where('id', $ref_lawuk_id)->delete();
            
            return ResponseFormatter::success([
                'Data ref lawuk berhasil dihapus'
            ]);
        } catch (Exception $error) {
            return ResponseFormatter::error([
                'message' => 'Something went wrong',
                'error' => $error,
            ], 'Delete Ref lawuk Failed', 500);
        }
    }
}
