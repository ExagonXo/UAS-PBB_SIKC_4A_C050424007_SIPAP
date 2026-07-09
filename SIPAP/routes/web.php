<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});

Route::get('/dashboard', function () {
    $user = auth()->user();
    
    // Stats calculation
    $totalAlat = \App\Models\Alat::count();
    $alatTersedia = \App\Models\Alat::where('stok', '>', 0)->count();
    $peminjamanAktif = \App\Models\Peminjaman::where('status', 'disetujui')->count();
    $menungguKonfirmasi = \App\Models\Peminjaman::where('status', 'pending')->count();
    $selesaiBulanIni = \App\Models\Peminjaman::where('status', 'selesai')
        ->whereMonth('updated_at', now()->month)
        ->count();

    // 5 Latest Borrowing requests
    $upcomingPeminjamans = \App\Models\Peminjaman::with(['user', 'alat'])
        ->latest()
        ->take(5)
        ->get();

    // Loans that are active (status = disetujui) waiting for return
    $pengembaliansPending = \App\Models\Peminjaman::with(['user', 'alat'])
        ->where('status', 'disetujui')
        ->latest()
        ->take(5)
        ->get();

    return view('dashboard', compact(
        'totalAlat',
        'alatTersedia',
        'peminjamanAktif',
        'menungguKonfirmasi',
        'selesaiBulanIni',
        'upcomingPeminjamans',
        'pengembaliansPending'
    ));
})->middleware(['auth', 'verified'])->name('dashboard');

// Menu SIPAP
Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('/alat', App\Http\Controllers\AdminAlatController::class)->names('alat');

    Route::get('/peminjaman', function () {
        $user = auth()->user();
        if ($user->role === 'admin') {
            $peminjamans = \App\Models\Peminjaman::with(['user', 'alat'])->latest()->get();
        } else {
            $peminjamans = \App\Models\Peminjaman::with(['user', 'alat'])->where('user_id', $user->id)->latest()->get();
        }
        return view('admin.peminjaman.index', compact('peminjamans'));
    })->name('peminjaman.index');

    Route::get('/peminjaman/{peminjaman}', function (\App\Models\Peminjaman $peminjaman) {
        $peminjaman->load(['user', 'alat']);
        return view('admin.peminjaman.show', compact('peminjaman'));
    })->name('peminjaman.show');

    Route::patch('/peminjaman/{peminjaman}/approve', function (\App\Models\Peminjaman $peminjaman) {
        if (auth()->user()->role !== 'admin') {
            abort(403);
        }

        $peminjaman->update(['status' => 'disetujui']);
        $peminjaman->alat->update(['status' => 'tersedia']);

        \App\Services\FirebaseService::sendNotification(
            $peminjaman->user,
            'Peminjaman Disetujui',
            'Silakan ambil alat ' . $peminjaman->alat->nama_alat . ' di laboratorium.'
        );

        return back()->with('success', 'Peminjaman berhasil disetujui');
    })->name('peminjaman.approve');

    Route::patch('/peminjaman/{peminjaman}/handover', function (\App\Models\Peminjaman $peminjaman) {
        if (auth()->user()->role !== 'admin') {
            abort(403);
        }

        $peminjaman->alat->update(['status' => 'dipinjam']);

        \App\Services\FirebaseService::sendNotification(
            $peminjaman->user,
            'Alat Diserahkan',
            'Alat ' . $peminjaman->alat->nama_alat . ' telah diserahkan. Masa peminjaman dimulai!'
        );

        return back()->with('success', 'Alat berhasil diserahkan ke peminjam');
    })->name('peminjaman.handover');

    Route::patch('/peminjaman/{peminjaman}/reject', function (\App\Models\Peminjaman $peminjaman) {
        if (auth()->user()->role !== 'admin') {
            abort(403);
        }

        $peminjaman->update(['status' => 'ditolak']);

        \App\Services\FirebaseService::sendNotification(
            $peminjaman->user,
            'Peminjaman Ditolak',
            'Pengajuan peminjaman alat ' . $peminjaman->alat->nama_alat . ' tidak dapat disetujui.'
        );

        return redirect()->route('peminjaman.index')->with('success', 'Peminjaman berhasil ditolak');
    })->name('peminjaman.reject');

    Route::get('/pengembalian', function () {
        $user = auth()->user();
        if ($user->role === 'admin') {
            $peminjamans = \App\Models\Peminjaman::with(['user', 'alat'])
                ->where('status', 'menunggu_kembali')
                ->orWhere(function($q) {
                    $q->where('status', 'disetujui')
                      ->whereHas('alat', function($aq) {
                          $aq->where('status', 'dipinjam');
                      });
                })
                ->latest()
                ->get();
                
            $pengembalians = \App\Models\Pengembalian::whereHas('peminjaman', function($q) {
                $q->where('status', 'selesai');
            })->with(['peminjaman.user', 'peminjaman.alat'])->latest()->get();
        } else {
            $peminjamans = \App\Models\Peminjaman::with(['user', 'alat'])
                ->where('user_id', $user->id)
                ->where(function($q) {
                    $q->where('status', 'menunggu_kembali')
                      ->orWhere(function($sq) {
                          $sq->where('status', 'disetujui')
                            ->whereHas('alat', function($aq) {
                                $aq->where('status', 'dipinjam');
                            });
                      });
                })
                ->latest()
                ->get();
                
            $pengembalians = \App\Models\Pengembalian::whereHas('peminjaman', function ($q) use ($user) {
                $q->where('user_id', $user->id)->where('status', 'selesai');
            })->with(['peminjaman.user', 'peminjaman.alat'])->latest()->get();
        }
        return view('admin.pengembalian.index', compact('peminjamans', 'pengembalians'));
    })->name('pengembalian.index');

    Route::post('/pengembalian', function (Illuminate\Http\Request $request) {
        if (auth()->user()->role !== 'admin') {
            abort(403);
        }
        
        $request->validate([
            'peminjaman_id' => 'required|exists:peminjamans,id',
            'kondisi_alat' => 'required|string',
            'denda' => 'required|numeric|min:0',
        ]);

        $peminjaman = \App\Models\Peminjaman::findOrFail($request->peminjaman_id);
        
        // Cari apakah record pengembalian sudah dibuat (diajukan oleh mahasiswa)
        $pengembalian = \App\Models\Pengembalian::where('peminjaman_id', $peminjaman->id)->first();
        
        if ($pengembalian) {
            $pengembalian->update([
                'tgl_kembali_aktual' => now(),
                'kondisi_alat' => $request->kondisi_alat,
                'denda' => $request->denda,
            ]);
        } else {
            $pengembalian = \App\Models\Pengembalian::create([
                'peminjaman_id' => $peminjaman->id,
                'tgl_kembali_aktual' => now(),
                'kondisi_alat' => $request->kondisi_alat,
                'denda' => $request->denda,
            ]);
        }

        // Update status peminjaman dan alat
        $peminjaman->update(['status' => 'selesai']);
        $peminjaman->alat->update(['status' => 'tersedia']);

        \App\Services\FirebaseService::sendNotification(
            $peminjaman->user,
            'Pengembalian Disetujui',
            'Pengembalian alat ' . $peminjaman->alat->nama_alat . ' telah disetujui oleh admin.' . ($request->denda > 0 ? ' Denda keterlambatan: Rp ' . number_format($request->denda) : '')
        );

        return back()->with('success', 'Pengembalian alat berhasil diverifikasi');
    })->name('pengembalian.store');

    // Route Placeholder
    Route::get('/admin/pengguna', function() {
        $users = \App\Models\User::all();
        return view('admin.pengguna.index', compact('users'));
    })->name('admin.pengguna.index');

    Route::get('/admin/notifikasi', function() {
        $notifikasis = \App\Models\Notifikasi::with('user')->latest()->get();
        return view('admin.notifikasi.index', compact('notifikasis'));
    })->name('admin.notifikasi.index');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
