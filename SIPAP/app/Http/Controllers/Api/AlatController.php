<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Alat;
use Illuminate\Http\Request;

class AlatController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(Alat::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_alat' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'stok' => 'required|integer|min:0',
            'status' => 'required|in:tersedia,dipinjam,rusak',
            'gambar' => 'nullable|string', // Simple string for now, could be file upload later
        ]);

        $alat = Alat::create($validated);

        return response()->json($alat, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Alat $alat)
    {
        return response()->json($alat);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Alat $alat)
    {
        $validated = $request->validate([
            'nama_alat' => 'string|max:255',
            'deskripsi' => 'nullable|string',
            'stok' => 'integer|min:0',
            'status' => 'in:tersedia,dipinjam,rusak',
            'gambar' => 'nullable|string',
        ]);

        $alat->update($validated);

        return response()->json($alat);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Alat $alat)
    {
        $alat->delete();

        return response()->json(['message' => 'Alat dihapus']);
    }
}
