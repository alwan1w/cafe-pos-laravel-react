<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Table;
use App\Http\Requests\TableRequest;
use App\Http\Resources\TableResource;

class TableController extends Controller
{
    public function index()
    {
        $tables = Table::orderBy('number')->get();
        return TableResource::collection($tables);
    }

    public function store(TableRequest $request)
    {
        $table = Table::create($request->validated());
        return new TableResource($table);
    }

    public function show(Table $table)
    {
        return new TableResource($table);
    }

    public function update(TableRequest $request, Table $table)
    {
        $table->update($request->validated());
        return new TableResource($table);
    }

    public function destroy(Table $table)
    {
        // Mencegah meja dihapus jika statusnya sedang terisi atau dipesan
        if ($table->status !== 'available') {
            return response()->json([
                'message' => 'Meja tidak dapat dihapus karena sedang ' . $table->status
            ], 400);
        }

        $table->delete();
        return response()->json(['message' => 'Meja berhasil dihapus']);
    }
}
