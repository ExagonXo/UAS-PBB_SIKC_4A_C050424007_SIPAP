<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Peminjaman;
use App\Models\Pengembalian;
use App\Services\FirebaseService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PengembalianController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'peminjaman_id' => 'required|exists:peminjamans,id',
            'kondisi_alat' => 'required|string',
        ]);

        $peminjaman = Peminjaman::findOrFail($request->peminjaman_id);

        if (auth()->user()->role !== 'admin' && $peminjaman->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized. Anda tidak diizinkan mengembalikan alat ini.'], 403);
        }

        if ($peminjaman->status !== 'disetujui') {
            return response()->json(['message' => 'Hanya peminjaman yang disetujui yang dapat dikembalikan.'], 400);
        }

        $tgl_kembali_aktual = Carbon::now();
        $denda = 0;

        // Hitung denda jika terlambat (5000 per hari)
        $tgl_rencana = Carbon::parse($peminjaman->tgl_kembali_rencana);
        if ($tgl_kembali_aktual->gt($tgl_rencana)) {
            $diff = $tgl_kembali_aktual->diffInDays($tgl_rencana);
            $denda = $diff * 5000;
        }

        $pengembalian = Pengembalian::create([
            'peminjaman_id' => $peminjaman->id,
            'tgl_kembali_aktual' => $tgl_kembali_aktual,
            'kondisi_alat' => $request->kondisi_alat,
            'denda' => $denda
        ]);

        // Update status peminjaman (menunggu konfirmasi pengembalian dari admin)
        $peminjaman->update(['status' => 'menunggu_kembali']);

        FirebaseService::sendNotification(
            $peminjaman->user, 
            'Pengajuan Pengembalian', 
            'Pengajuan pengembalian untuk alat ' . $peminjaman->alat->nama_alat . ' telah dikirim dan sedang menunggu verifikasi admin.'
        );

        return response()->json($pengembalian, 201);
    }
}
