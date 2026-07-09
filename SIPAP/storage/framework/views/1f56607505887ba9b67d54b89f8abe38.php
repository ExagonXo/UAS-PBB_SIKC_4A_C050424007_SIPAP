<?php $__env->startSection('page-title', 'Pengembalian Alat'); ?>

<?php $__env->startSection('content'); ?>
    <div class="space-y-6" x-data="{ activeTab: 'aktif', showModal: false, selectedPeminjamanId: null, selectedAlatName: '' }">

        <!-- Tab Navigation -->
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-6" aria-label="Tabs">
                <button @click="activeTab = 'aktif'" :class="activeTab === 'aktif' ? 'border-[#0EA5E9] text-[#0EA5E9] font-semibold border-b-2' : 'border-transparent text-[#94A3B8] hover:text-[#334155]'" class="pb-4 px-1 text-sm font-medium transition cursor-pointer whitespace-nowrap">
                    Menunggu Pengembalian
                    <span class="ml-1.5 bg-[#0EA5E9]/10 text-[#0EA5E9] text-[10px] font-bold px-1.5 py-0.5 rounded-full"><?php echo e($peminjamans->count()); ?></span>
                </button>
                <button @click="activeTab = 'riwayat'" :class="activeTab === 'riwayat' ? 'border-[#0EA5E9] text-[#0EA5E9] font-semibold border-b-2' : 'border-transparent text-[#94A3B8] hover:text-[#334155]'" class="pb-4 px-1 text-sm font-medium transition cursor-pointer whitespace-nowrap">
                    Riwayat Pengembalian
                    <span class="ml-1.5 bg-slate-100 text-slate-500 text-[10px] font-bold px-1.5 py-0.5 rounded-full"><?php echo e($pengembalians->count()); ?></span>
                </button>
            </nav>
        </div>

        <?php if(session('success')): ?>
        <div class="bg-emerald-50 text-[#10B981] text-xs font-semibold p-4 rounded-xl border border-emerald-100 flex items-center gap-3 shadow-sm">
            <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span><?php echo e(session('success')); ?></span>
        </div>
        <?php endif; ?>

        <!-- Tab: Menunggu Pengembalian -->
        <div x-show="activeTab === 'aktif'" x-transition>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100 text-left text-sm">
                        <thead class="bg-gray-50 text-xs font-semibold text-[#94A3B8] uppercase tracking-wider">
                            <tr>
                                <th class="px-6 py-3 w-14">No</th>
                                <th class="px-6 py-3">Peminjam</th>
                                <th class="px-6 py-3">Alat</th>
                                <th class="px-6 py-3">Tgl Pinjam</th>
                                <th class="px-6 py-3">Tgl Rencana Kembali</th>
                                <th class="px-6 py-3">Keterlambatan</th>
                                <th class="px-6 py-3">Status</th>
                                <?php if(Auth::user()->role === 'admin'): ?>
                                <th class="px-6 py-3 text-right">Aksi</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white text-[#334155]">
                             <?php $__empty_1 = true; $__currentLoopData = $peminjamans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                             <?php
                                 $isLate = \Carbon\Carbon::now()->gt($item->tgl_kembali_rencana);
                                 $daysLate = $isLate ? \Carbon\Carbon::now()->diffInDays($item->tgl_kembali_rencana) : 0;
                             ?>
                             <tr class="hover:bg-slate-50/50 transition <?php echo e($isLate ? 'bg-red-50/30' : ''); ?>">
                                 <td class="px-6 py-4 text-slate-400 font-medium"><?php echo e($index + 1); ?></td>
                                 <td class="px-6 py-4 whitespace-nowrap">
                                     <div class="font-medium text-[#334155]"><?php echo e($item->user->name ?? 'N/A'); ?></div>
                                     <div class="text-[10px] text-slate-400"><?php echo e($item->user->email ?? ''); ?></div>
                                 </td>
                                 <td class="px-6 py-4 whitespace-nowrap font-medium"><?php echo e($item->alat->nama_alat ?? 'N/A'); ?></td>
                                 <td class="px-6 py-4 text-slate-500 whitespace-nowrap"><?php echo e($item->tgl_pinjam->format('d M Y')); ?></td>
                                 <td class="px-6 py-4 whitespace-nowrap">
                                     <span class="<?php echo e($isLate ? 'text-red-600 font-semibold' : 'text-slate-500'); ?>">
                                         <?php echo e($item->tgl_kembali_rencana->format('d M Y')); ?>

                                     </span>
                                 </td>
                                 <td class="px-6 py-4 whitespace-nowrap">
                                     <?php if($isLate): ?>
                                         <span class="inline-flex items-center gap-1 bg-red-50 text-red-600 px-2.5 py-0.5 rounded-full text-xs font-semibold border border-red-100">
                                             <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                 <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                             </svg>
                                             Terlambat <?php echo e($daysLate); ?> Hari
                                         </span>
                                     <?php else: ?>
                                         <span class="inline-flex items-center bg-emerald-50 text-emerald-600 px-2.5 py-0.5 rounded-full text-xs font-semibold">
                                             Tepat Waktu
                                         </span>
                                     <?php endif; ?>
                                 </td>
                                 <td class="px-6 py-4 whitespace-nowrap font-medium">
                                     <?php if($item->status === 'menunggu_kembali'): ?>
                                         <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-50 px-2.5 py-0.5 text-xs font-semibold text-[#F59E0B] border border-amber-200">
                                             <span class="w-1.5 h-1.5 rounded-full bg-[#F59E0B] animate-pulse"></span>
                                             Menunggu Verifikasi
                                         </span>
                                     <?php else: ?>
                                         <span class="inline-flex items-center gap-1.5 rounded-full bg-blue-50 px-2.5 py-0.5 text-xs font-semibold text-blue-600 border border-blue-200">
                                             Sedang Dipinjam
                                         </span>
                                     <?php endif; ?>
                                 </td>
                                 <?php if(Auth::user()->role === 'admin'): ?>
                                 <td class="px-6 py-4 text-right whitespace-nowrap">
                                     <button type="button"
                                         class="bg-[#0369A1] hover:bg-[#0C4A6E] text-white text-xs font-semibold rounded-lg px-3 py-1.5 transition active:scale-95 shadow-sm"
                                         @click="showModal = true; selectedPeminjamanId = '<?php echo e($item->id); ?>'; selectedAlatName = '<?php echo e($item->alat->nama_alat ?? ''); ?>'">
                                         Verifikasi Kembali
                                     </button>
                                 </td>
                                 <?php endif; ?>
                             </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center gap-2 text-gray-400">
                                        <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <p class="text-xs">Tidak ada peminjaman yang menunggu pengembalian.</p>
                                    </div>
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Tab: Riwayat Pengembalian -->
        <div x-show="activeTab === 'riwayat'" x-transition>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100 text-left text-sm">
                        <thead class="bg-gray-50 text-xs font-semibold text-[#94A3B8] uppercase tracking-wider">
                            <tr>
                                <th class="px-6 py-3 w-14">No</th>
                                <th class="px-6 py-3">Peminjam</th>
                                <th class="px-6 py-3">Alat</th>
                                <th class="px-6 py-3">Tgl Dikembalikan</th>
                                <th class="px-6 py-3">Kondisi Alat</th>
                                <th class="px-6 py-3">Denda</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white text-[#334155]">
                            <?php $__empty_1 = true; $__currentLoopData = $pengembalians; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="px-6 py-4 text-slate-400 font-medium"><?php echo e($index + 1); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="font-medium"><?php echo e($p->peminjaman->user->name ?? 'N/A'); ?></div>
                                    <div class="text-[10px] text-slate-400"><?php echo e($p->peminjaman->user->email ?? ''); ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap font-medium"><?php echo e($p->peminjaman->alat->nama_alat ?? 'N/A'); ?></td>
                                <td class="px-6 py-4 text-slate-500 whitespace-nowrap"><?php echo e(\Carbon\Carbon::parse($p->tgl_kembali_aktual)->format('d M Y, H:i')); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php if(str_contains(strtolower($p->kondisi_alat ?? ''), 'rusak')): ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-red-50 text-red-600">
                                            <?php echo e($p->kondisi_alat); ?>

                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-600">
                                            <?php echo e($p->kondisi_alat ?? 'Baik'); ?>

                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php if(($p->denda ?? 0) > 0): ?>
                                        <span class="font-semibold text-red-600">Rp <?php echo e(number_format($p->denda, 0, ',', '.')); ?></span>
                                    <?php else: ?>
                                        <span class="text-slate-400 text-xs">Tidak ada</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-400">
                                    <div class="flex flex-col items-center gap-2">
                                        <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2"/>
                                        </svg>
                                        <p class="text-xs">Belum ada riwayat pengembalian.</p>
                                    </div>
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- ============================================================
             MODAL: Verifikasi Pengembalian
        ============================================================ -->
        <div x-show="showModal" class="fixed inset-0 z-50 flex items-center justify-center" x-cloak>
            <!-- Backdrop -->
            <div class="absolute inset-0 bg-[#0C4A6E]/40 backdrop-blur-sm" @click="showModal = false"></div>

            <!-- Modal Box -->
            <div class="relative bg-white rounded-2xl shadow-2xl shadow-sky-900/10 border border-slate-100 w-full max-w-md mx-4 p-6" @click.stop>
                <div class="flex justify-between items-start mb-5">
                    <div>
                        <h3 class="text-base font-semibold text-[#334155]">Verifikasi Pengembalian</h3>
                        <p class="text-xs text-[#94A3B8] mt-0.5">Periksa kondisi alat sebelum konfirmasi.</p>
                    </div>
                    <button @click="showModal = false" class="p-1.5 hover:bg-slate-100 rounded-lg transition text-slate-400 hover:text-slate-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <!-- Info Alat -->
                <div class="bg-[#F0F9FF] border border-[#BAE6FD] rounded-xl p-3 mb-5 flex items-center gap-3">
                    <svg class="w-5 h-5 text-[#0EA5E9] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                    <p class="text-sm font-medium text-[#0C4A6E]" x-text="selectedAlatName"></p>
                </div>

                <form action="<?php echo e(route('pengembalian.store')); ?>" method="POST" class="space-y-4">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="peminjaman_id" :value="selectedPeminjamanId">

                    <div>
                        <label class="text-sm font-medium text-[#334155] mb-1 block">Kondisi Alat *</label>
                        <select name="kondisi_alat" required class="border border-gray-200 rounded-lg px-3.5 py-2 text-sm w-full focus:outline-none focus:ring-2 focus:ring-[#38BDF8] focus:border-[#0EA5E9] transition">
                            <option value="Baik">Baik — Tidak ada kerusakan</option>
                            <option value="Baik dengan goresan">Baik dengan goresan kecil</option>
                            <option value="Rusak ringan">Rusak ringan</option>
                            <option value="Rusak berat">Rusak berat</option>
                        </select>
                    </div>

                    <div>
                        <label class="text-sm font-medium text-[#334155] mb-1 block">Denda (Rp)</label>
                        <input type="number" name="denda" value="0" min="0" class="border border-gray-200 rounded-lg px-3.5 py-2 text-sm w-full focus:outline-none focus:ring-2 focus:ring-[#38BDF8] focus:border-[#0EA5E9] transition">
                        <p class="text-xs text-[#94A3B8] mt-1">Isi 0 jika tidak ada denda.</p>
                    </div>

                    <div class="flex gap-3 pt-2">
                        <button type="button" @click="showModal = false" class="flex-1 border border-gray-200 rounded-lg text-sm font-semibold text-[#334155] py-2 hover:bg-slate-50 transition active:scale-95">
                            Batal
                        </button>
                        <button type="submit" class="flex-1 bg-[#0369A1] hover:bg-[#0C4A6E] text-white text-sm font-semibold rounded-lg py-2 transition active:scale-95 hover:shadow-lg hover:shadow-sky-600/10">
                            Konfirmasi Pengembalian
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\UAS Pak Arifin\SIPAP\resources\views/admin/pengembalian/index.blade.php ENDPATH**/ ?>