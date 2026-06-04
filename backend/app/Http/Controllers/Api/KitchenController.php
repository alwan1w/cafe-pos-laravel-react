<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TransactionItem;

class KitchenController extends Controller
{
    /**
     * Menampilkan daftar order makanan yang belum selesai
     */
    public function index()
    {
        $orders = TransactionItem::with(['transaction:id,invoice,customer_name,table_number,created_at'])
            ->whereHas('transaction', function ($query) {
                $query->where('status', 'completed'); // Hanya dari transaksi valid
            })
            ->where('product_type', 'food')
            ->whereIn('status', ['new', 'preparing'])
            ->orderBy('id', 'asc') // Urutkan dari yang paling lama masuk (First In, First Out)
            ->get();

        return response()->json([
            'message' => 'Daftar Order Kitchen',
            'data' => $orders
        ]);
    }

    /**
     * Update status order makanan
     */
    public function updateStatus(Request $request, TransactionItem $item)
    {
        $request->validate([
            'status' => 'required|in:new,preparing,done'
        ]);

        if ($item->product_type !== 'food') {
            return response()->json(['message' => 'Item ini bukan makanan.'], 400);
        }

        $item->update(['status' => $request->status]);

        return response()->json([
            'message' => 'Status berhasil diupdate menjadi ' . $request->status,
            'data' => $item
        ]);
    }
}
