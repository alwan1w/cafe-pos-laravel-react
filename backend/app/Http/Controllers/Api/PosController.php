<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\Product;
use App\Models\Voucher;
use App\Models\Table;
use App\Http\Requests\PosTransactionRequest;
use App\Http\Resources\TransactionResource;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PosController extends Controller
{
    /**
     * Endpoint untuk mengecek apakah voucher valid (digunakan saat kasir mengetik kode)
     */
    public function checkVoucher(Request $request)
    {
        $request->validate(['code' => 'required|string']);

        $voucher = Voucher::where('code', $request->code)->first();

        if (!$voucher || !$voucher->is_active) {
            return response()->json(['message' => 'Voucher tidak ditemukan atau tidak aktif'], 404);
        }

        if ($voucher->expired_at < Carbon::now()) {
            return response()->json(['message' => 'Voucher sudah kedaluwarsa'], 400);
        }

        if ($voucher->quota !== null && $voucher->used_count >= $voucher->quota) {
            return response()->json(['message' => 'Kuota voucher sudah habis'], 400);
        }

        return response()->json([
            'message' => 'Voucher valid',
            'data' => $voucher
        ]);
    }

    /**
     * Endpoint utama untuk memproses transaksi kasir
     */
    public function store(PosTransactionRequest $request)
    {
        DB::beginTransaction();
        try {
            // 1. Ambil data produk berdasarkan keranjang (Mencegah harga dimanipulasi dari frontend)
            $items = $request->items;
            $productIds = collect($items)->pluck('product_id');
            $products = Product::whereIn('id', $productIds)->get()->keyBy('id');

            $subtotal = 0;
            $transactionItemsData = [];

            // 2. Kalkulasi Subtotal & Siapkan Data Items
            foreach ($items as $item) {
                $product = $products[$item['product_id']];
                $itemSubtotal = $product->price * $item['qty'];
                $subtotal += $itemSubtotal;

                $transactionItemsData[] = [
                    'product_id' => $product->id,
                    'product_name' => $product->name, // Snapshot nama
                    'product_type' => $product->type, // Snapshot tipe (food/drink) untuk dikirim ke kitchen/bar
                    'qty' => $item['qty'],
                    'unit_price' => $product->price,  // Snapshot harga saat ini
                    'subtotal' => $itemSubtotal,
                    'notes' => $item['notes'] ?? null,
                ];
            }

            // 3. Kalkulasi Voucher (Diskon)
            $discountAmount = 0;
            $voucherId = null;

            if ($request->voucher_code) {
                $voucher = Voucher::where('code', $request->voucher_code)->first();

                // Cek ulang validitas voucher
                if ($voucher && $voucher->is_active && $voucher->expired_at > Carbon::now() && ($voucher->quota === null || $voucher->used_count < $voucher->quota)) {
                    if ($subtotal >= $voucher->min_purchase) {
                        $voucherId = $voucher->id;

                        if ($voucher->type === 'fixed') {
                            $discountAmount = $voucher->discount_value;
                        } else { // Persentase
                            $discountCalc = ($subtotal * $voucher->discount_value) / 100;
                            // Batasi diskon jika ada max_discount
                            $discountAmount = ($voucher->max_discount && $discountCalc > $voucher->max_discount)
                                              ? $voucher->max_discount
                                              : $discountCalc;
                        }

                        // Increment penggunaan voucher
                        $voucher->increment('used_count');
                    }
                }
            }

            // Mencegah diskon lebih besar dari subtotal
            if ($discountAmount > $subtotal) {
                $discountAmount = $subtotal;
            }

            // 4. Kalkulasi Pajak (Asumsi PB1 / PPN 11% dari nominal setelah diskon)
            $taxRate = 0.11;
            $amountAfterDiscount = $subtotal - $discountAmount;
            $taxAmount = (int) round($amountAfterDiscount * $taxRate);

            // 5. Kalkulasi Grand Total
            $grandTotal = $amountAfterDiscount + $taxAmount;

            // 6. Validasi Pembayaran Cash (Uang yang diterima harus >= Grand Total)
            $paymentAmount = $request->payment_amount;
            $changeAmount = 0;
            $paymentStatus = 'unpaid';

            if ($request->payment_method === 'cash') {
                if ($paymentAmount < $grandTotal) {
                    return response()->json(['message' => 'Nominal pembayaran cash kurang dari total tagihan.'], 400);
                }
                $changeAmount = $paymentAmount - $grandTotal;
                $paymentStatus = 'paid';
            } elseif ($request->payment_method === 'qris') {
                $paymentAmount = $grandTotal; // Asumsi QRIS selalu pas
                $paymentStatus = 'paid';
            }

            // 7. Generate Nomor Invoice (Contoh: INV-20260604-0001)
            $today = Carbon::now()->format('Ymd');
            $latestTransaction = Transaction::whereDate('created_at', Carbon::today())->orderBy('id', 'desc')->first();
            $sequence = $latestTransaction ? (int) substr($latestTransaction->invoice, -4) + 1 : 1;
            $invoiceNumber = 'INV-' . $today . '-' . str_pad($sequence, 4, '0', STR_PAD_LEFT);

            // 8. Ambil Data Meja (Jika Ada) & Update Status Meja
            $tableNumber = null;
            if ($request->table_id) {
                $table = Table::find($request->table_id);
                $tableNumber = $table->number;
                // Tandai meja sedang diduduki tamu
                $table->update(['status' => 'occupied']);
            }

            // 9. Simpan Transaksi Induk
            $transaction = Transaction::create([
                'invoice' => $invoiceNumber,
                'customer_name' => $request->customer_name,
                'table_id' => $request->table_id,
                'table_number' => $tableNumber,
                'cashier_id' => auth()->id(), // Mengambil UUID kasir yang sedang login
                'voucher_id' => $voucherId,
                'subtotal' => $subtotal,
                'discount_amount' => $discountAmount,
                'tax_amount' => $taxAmount,
                'grand_total' => $grandTotal,
                'payment_method' => $request->payment_method,
                'payment_amount' => $paymentAmount,
                'change_amount' => $changeAmount,
                'payment_status' => $paymentStatus,
                'status' => 'completed', // Sesuai PRD workflow kasir, setelah bayar status completed
            ]);

            // 10. Simpan Item Transaksi
            foreach ($transactionItemsData as $itemData) {
                $itemData['transaction_id'] = $transaction->id;
                TransactionItem::create($itemData);
            }

            DB::commit();

            // Load relasi sebelum dikirim ke frontend
            return response()->json([
                'message' => 'Transaksi berhasil diproses',
                'data' => new TransactionResource($transaction->load(['items', 'cashier.profile']))
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()], 500);
        }
    }
}
