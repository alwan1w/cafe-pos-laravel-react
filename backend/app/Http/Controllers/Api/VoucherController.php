<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Voucher;
use App\Http\Requests\VoucherRequest;
use App\Http\Resources\VoucherResource;

class VoucherController extends Controller
{
    public function index()
    {
        $vouchers = Voucher::latest()->get();
        return VoucherResource::collection($vouchers);
    }

    public function store(VoucherRequest $request)
    {
        $data = $request->validated();

        // Pastikan default value diatur jika null
        $data['min_purchase'] = $data['min_purchase'] ?? 0;

        $voucher = Voucher::create($data);
        return new VoucherResource($voucher);
    }

    public function show(Voucher $voucher)
    {
        return new VoucherResource($voucher);
    }

    public function update(VoucherRequest $request, Voucher $voucher)
    {
        $data = $request->validated();
        $data['min_purchase'] = $data['min_purchase'] ?? 0;

        $voucher->update($data);
        return new VoucherResource($voucher);
    }

    public function destroy(Voucher $voucher)
    {
        $voucher->delete();
        return response()->json(['message' => 'Voucher berhasil dihapus']);
    }
}
