<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Catering;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    public function all(Request $request)
    {
        $id = $request->input('id');
        // $limit = $request->input('limit', 6);
        $status = $request->input('status');

        if ($id) {
            $transaction = Transaction::with(['items.product'])->find($id);

            $admin = User::where('roles', 'ADMIN')->first();
            $telp = $admin->phone;

            if ($transaction) {
                return ResponseFormatter::success(
                    $telp,
                    $transaction,
                    'Data transaksi berhasil diambil'
                );
            } else {
                return ResponseFormatter::error(
                    null,
                    'Data transaksi tidak ada',
                    404
                );
            }
        }
        $transaction = Transaction::with(['items.product', 'caterings.refSayurs', 'caterings.refLawuks'])
            ->where('users_id', Auth::user()->id)->orderByDesc('id');

        if ($status) {
            $transaction->where('status', $status);
        }

        return ResponseFormatter::success(
            $transaction->paginate(),
            'Data list transaksi berhasil diambil'
        );
    }

    public function checkout(Request $request)
    {

        $request->validate([

            'total_price' => 'required',
            'shipping_price' => 'required',
            'status' => 'required|in:PENDING,SUCCESS,CANCELLED,FAILED,SHIPPING,SHIPPED,PROCESS',
        ]);

        if ($request->is_catering == '1') {
            $catering = Catering::create([
                'users_id' => Auth::user()->id,
                'sayur_id' => $request->sayur,
                'lauk_id' => $request->lauk,
                'harga' => $request->total_price,
                'tanggal' => $request->tanggal,
                'jumlah' => $request->jumlah,
            ]);



            $transaction = Transaction::create([
                'users_id' => Auth::user()->id,
                'address' => $request->address,
                'total_price' => $request->total_price,
                'shipping_price' => $request->shipping_price,
                'status' => $request->status,
                'is_catering' => $request->is_catering,
                'catering_id' => $catering->id,
            ]);
        } else {
            $items = json_decode($request->items);


            $transaction = Transaction::create([
                'users_id' => Auth::user()->id,
                'address' => $request->address,
                'total_price' => $request->total_price,
                'shipping_price' => $request->shipping_price,
                'status' => $request->status,
                'is_catering' => $request->is_catering,
            ]);


            for ($i = 0; $i < count($items); $i++) {
                TransactionItem::create([
                    'users_id' => Auth::user()->id,
                    'products_id' => $items[$i]->id,
                    'transactions_id' => $transaction->id,
                    'quantity' => $items[$i]->quantity,
                ]);
            }
        }

        return ResponseFormatter::success('OK', 'Transaksi berhasil');
    }

    public function konfirmasi(Request $request)
    {

        $request->validate([
            'status' => 'required|in:PENDING,SUCCESS,CANCELLED,FAILED,SHIPPING,SHIPPED,PROCESS',
        ]);

        $transaction = Transaction::find($request->id);

        if ($transaction) {
            $transaction->status = $request->status;
            $transaction->save();

            // Status catering berhasil diperbarui
            // Lakukan tindakan lain di sini jika diperlukan
        }

        return ResponseFormatter::success('OK', 'Konfirmasi berhasil');
    }
}
