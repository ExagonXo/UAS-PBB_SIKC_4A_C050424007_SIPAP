<?php

namespace App\Http\Controllers;

use App\Models\Alat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminAlatController extends Controller
{
    public function index(Request $request)
    {
        $query = Alat::query();

        // Simple search filter
        if ($request->filled('search')) {
            $query->where('nama_alat', 'like', '%' . $request->search . '%')
                  ->orWhere('merk', 'like', '%' . $request->search . '%');
        }

        // Simple status filter
        if ($request->filled('status')) {
            if ($request->status === 'tersedia') {
                $query->where('stok', '>', 0);
            } else {
                $query->where('stok', '<=', 0);
            }
        }

        $alats = $query->latest()->paginate(10);

        return view('admin.alat.index', compact('alats'));
    }

    public function create()
    {
        return view('admin.alat.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_alat' => 'required|string|max:255',
            'merk' => 'nullable|string|max:255',
            'stok' => 'required|integer|min:0',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'deskripsi' => 'nullable|string',
            'status' => 'required|in:tersedia,rusak,dipinjam',
        ]);

        $data = $request->only(['nama_alat', 'merk', 'stok', 'deskripsi', 'status']);

        if ($request->hasFile('gambar')) {
            $data['gambar'] = $request->file('gambar')->store('alats', 'public');
        }

        Alat::create($data);

        return redirect()->route('alat.index')->with('success', 'Alat berhasil ditambahkan');
    }

    public function edit(Alat $alat)
    {
        return view('admin.alat.edit', compact('alat'));
    }

    public function update(Request $request, Alat $alat)
    {
        $request->validate([
            'nama_alat' => 'required|string|max:255',
            'merk' => 'nullable|string|max:255',
            'stok' => 'required|integer|min:0',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'deskripsi' => 'nullable|string',
            'status' => 'required|in:tersedia,rusak,dipinjam',
        ]);

        $data = $request->only(['nama_alat', 'merk', 'stok', 'deskripsi', 'status']);

        if ($request->hasFile('gambar')) {
            // Delete old gambar
            if ($alat->gambar) {
                Storage::disk('public')->delete($alat->gambar);
            }
            $data['gambar'] = $request->file('gambar')->store('alats', 'public');
        }

        $alat->update($data);

        return redirect()->route('alat.index')->with('success', 'Alat berhasil diperbarui');
    }

    public function destroy(Alat $alat)
    {
        if ($alat->gambar) {
            Storage::disk('public')->delete($alat->gambar);
        }
        $alat->delete();

        return redirect()->route('alat.index')->with('success', 'Alat berhasil dihapus');
    }
}
