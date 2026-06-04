<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Exports\SalesExport;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function export(Request $request)
    {
        $month = $request->query('month', Carbon::now()->month);
        $year = $request->query('year', Carbon::now()->year);

        $fileName = 'Laporan_Penjualan_' . $year . '_' . str_pad($month, 2, '0', STR_PAD_LEFT) . '.xlsx';

        // Langsung return stream file Excel ke browser
        return Excel::download(new SalesExport($month, $year), $fileName);
    }
}
