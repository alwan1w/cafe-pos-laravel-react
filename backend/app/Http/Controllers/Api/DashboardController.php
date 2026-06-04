<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Data Summary Card (Hari Ini)
     */
    public function summary(Request $request)
    {
        $today = Carbon::today();

        $transactionsToday = Transaction::where('status', 'completed')
            ->whereDate('created_at', $today);

        $totalRevenue = (clone $transactionsToday)->sum('grand_total');
        $totalTransactions = (clone $transactionsToday)->count();
        $averageTransaction = $totalTransactions > 0 ? $totalRevenue / $totalTransactions : 0;

        $itemsSold = TransactionItem::whereHas('transaction', function ($query) use ($today) {
            $query->where('status', 'completed')->whereDate('created_at', $today);
        })->sum('qty');

        return response()->json([
            'revenue_today' => $totalRevenue,
            'total_transactions' => $totalTransactions,
            'average_transaction' => round($averageTransaction),
            'items_sold' => $itemsSold,
        ]);
    }

    /**
     * Grafik Penjualan Harian (Bulan Ini)
     */
    public function chartSales()
    {
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        $sales = Transaction::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(grand_total) as revenue')
            )
            ->where('status', 'completed')
            ->whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        return response()->json($sales);
    }

    /**
     * Grafik Produk Terlaris (Top 10 Bulan Ini)
     */
    public function chartProducts()
    {
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        $topProducts = TransactionItem::select('product_name', DB::raw('SUM(qty) as total_sold'))
            ->whereHas('transaction', function ($query) use ($currentMonth, $currentYear) {
                $query->where('status', 'completed')
                      ->whereMonth('created_at', $currentMonth)
                      ->whereYear('created_at', $currentYear);
            })
            ->groupBy('product_name')
            ->orderBy('total_sold', 'desc')
            ->limit(10)
            ->get();

        return response()->json($topProducts);
    }

    /**
     * Grafik Metode Pembayaran Pie Chart (Bulan Ini)
     */
    public function chartPayment()
    {
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        $payments = Transaction::select('payment_method', DB::raw('COUNT(*) as total'))
            ->where('status', 'completed')
            ->whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->groupBy('payment_method')
            ->get();

        return response()->json($payments);
    }

    /**
     * Grafik Jam Ramai Bar Chart (Bulan Ini)
     */
    public function chartPeakHours()
    {
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        $peakHours = Transaction::select(
                DB::raw('HOUR(created_at) as hour'),
                DB::raw('COUNT(*) as total_transactions')
            )
            ->where('status', 'completed')
            ->whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->groupBy('hour')
            ->orderBy('hour', 'asc')
            ->get();

        return response()->json($peakHours);
    }
}
