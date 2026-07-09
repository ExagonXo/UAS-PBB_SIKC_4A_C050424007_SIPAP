<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Alat;
use App\Models\Peminjaman;
use App\Services\FirebaseService;
use Illuminate\Http\Request;

class PeminjamanController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        if ($user->role === 'admin') {
            return response()->json(Peminjaman::with(['user', 'alat'])->get());
        }

        return response()->json(Peminjaman::with('alat')->where('user_id', $user->id)->get());
    }

    public function store(Request $request)
    {
        $request->validate([
            'alat_id' => 'required|exists:alats,id',
            'tgl_pinjam' => 'required|date',
            'tgl_kembali_rencana' => 'required|date|after:tgl_pinjam',
        ]);

        $alat = Alat::findOrFail($request->alat_id);

        if ($alat->status !== 'tersedia' || $alat->stok <= 0) {
            return response()->json(['message' => 'Alat tidak tersedia untuk dipinjam.'], 400);
        }

        $peminjaman = Peminjaman::create([
            'user_id' => $request->user()->id,
            'alat_id' => $request->alat_id,
            'tgl_pinjam' => $request->tgl_pinjam,
            'tgl_kembali_rencana' => $request->tgl_kembali_rencana,
            'status' => 'pending'
        ]);

        // Opsional: Notifikasi ke Admin (Jika ada user admin target)
        // FirebaseService::sendNotification($adminUser, 'Pengajuan Baru', 'Seorang user meminjam ' . $alat->nama_alat);

        return response()->json($peminjaman, 201);
    }

    public function updateStatus(Request $request, Peminjaman $peminjaman)
    {
        $request->validate([
            'status' => 'required|in:disetujui,ditolak,selesai'
        ]);

        $oldStatus = $peminjaman->status;
        $peminjaman->update(['status' => $request->status]);

        if ($request->status === 'disetujui') {
            $peminjaman->alat->update(['status' => 'dipinjam']);
            FirebaseService::sendNotification($peminjaman->user, 'Peminjaman Disetujui', 'Silakan ambil alat ' . $peminjaman->alat->nama_alat);
        } elseif ($request->status === 'ditolak') {
            $peminjaman->alat->update(['status' => 'tersedia']);
            FirebaseService::sendNotification($peminjaman->user, 'Peminjaman Ditolak', 'Maaf, pengajuan pinjaman ' . $peminjaman->alat->nama_alat . ' belum disetujui.');
        } elseif ($request->status === 'selesai' && $oldStatus !== 'selesai') {
            $peminjaman->alat->update(['status' => 'tersedia']);
        }

        return response()->json($peminjaman);
    }
}
