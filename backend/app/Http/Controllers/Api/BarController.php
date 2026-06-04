<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TransactionItem;

class BarController extends Controller
{
    public function index()
    {
        $orders = TransactionItem::with(['transaction:id,invoice,customer_name,table_number,created_at'])
            ->whereHas('transaction', function ($query) {
                $query->where('status', 'completed');
            })
            ->where('product_type', 'drink')
            ->whereIn('status', ['new', 'preparing'])
            ->orderBy('id', 'asc')
            ->get();

        return response()->json([
            'message' => 'Daftar Order Bar',
            'data' => $orders
        ]);
    }

    public function updateStatus(Request $request, TransactionItem $item)
    {
        $request->validate([
            'status' => 'required|in:new,preparing,done'
        ]);

        if ($item->product_type !== 'drink') {
            return response()->json(['message' => 'Item ini bukan minuman.'], 400);
        }

        $item->update(['status' => $request->status]);

        return response()->json([
            'message' => 'Status berhasil diupdate menjadi ' . $request->status,
            'data' => $item
        ]);
    }
}
