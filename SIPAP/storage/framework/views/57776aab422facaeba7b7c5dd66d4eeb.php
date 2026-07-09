<?php $__env->startSection('page-title', 'Daftar Peminjaman'); ?>

<?php $__env->startSection('content'); ?>
    <div class="space-y-6" x-data="{ activeTab: 'semua' }">
        <!-- Tab Navigation (Underline Style) -->
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-6 overflow-x-auto" aria-label="Tabs">
                <button @click="activeTab = 'semua'" :class="activeTab === 'semua' ? 'border-[#0EA5E9] text-[#0EA5E9] font-semibold border-b-2' : 'border-transparent text-[#94A3B8] hover:text-[#334155]'" class="pb-4 px-1 text-sm font-medium transition cursor-pointer whitespace-nowrap">
                    Semua
                </button>
                <button @click="activeTab = 'pending'" :class="activeTab === 'pending' ? 'border-[#0EA5E9] text-[#0EA5E9] font-semibold border-b-2' : 'border-transparent text-[#94A3B8] hover:text-[#334155]'" class="pb-4 px-1 text-sm font-medium transition cursor-pointer whitespace-nowrap flex items-center gap-1.5">
                    Menunggu Konfirmasi
                    <span class="bg-[#EF4444] text-white text-[10px] px-1.5 py-0.5 rounded-full font-bold">
                        <?php echo e($peminjamans->where('status', 'pending')->count()); ?>

                    </span>
                </button>
                <button @click="activeTab = 'disetujui'" :class="activeTab === 'disetujui' ? 'border-[#0EA5E9] text-[#0EA5E9] font-semibold border-b-2' : 'border-transparent text-[#94A3B8] hover:text-[#334155]'" class="pb-4 px-1 text-sm font-medium transition cursor-pointer whitespace-nowrap">
                    Siap Diambil
                </button>
                <button @click="activeTab = 'dipinjam'" :class="activeTab === 'dipinjam' ? 'border-[#0EA5E9] text-[#0EA5E9] font-semibold border-b-2' : 'border-transparent text-[#94A3B8] hover:text-[#334155]'" class="pb-4 px-1 text-sm font-medium transition cursor-pointer whitespace-nowrap">
                    Dipinjam
                </button>
                <button @click="activeTab = 'selesai'" :class="activeTab === 'selesai' ? 'border-[#0EA5E9] text-[#0EA5E9] font-semibold border-b-2' : 'border-transparent text-[#94A3B8] hover:text-[#334155]'" class="pb-4 px-1 text-sm font-medium transition cursor-pointer whitespace-nowrap">
                    Selesai
                </button>
                <button @click="activeTab = 'ditolak'" :class="activeTab === 'ditolak' ? 'border-[#0EA5E9] text-[#0EA5E9] font-semibold border-b-2' : 'border-transparent text-[#94A3B8] hover:text-[#334155]'" class="pb-4 px-1 text-sm font-medium transition cursor-pointer whitespace-nowrap">
                    Ditolak
                </button>
            </nav>
        </div>

        <?php if(session('success')): ?>
        <div class="bg-emerald-50 text-[#10B981] text-xs font-semibold p-4 rounded-xl border border-emerald-100 flex items-center gap-3 shadow-sm">
            <svg class="h-5 w-5 shrink-0 text-[#10B981]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span><?php echo e(session('success')); ?></span>
        </div>
        <?php endif; ?>

        <!-- Card Peminjaman Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100 text-left text-sm">
                    <thead class="bg-gray-50 text-xs font-semibold text-[#94A3B8] uppercase tracking-wider">
                        <tr>
                            <th class="px-6 py-3 w-16">No</th>
                            <th class="px-6 py-3">Peminjam</th>
                            <th class="px-6 py-3">Alat</th>
                            <th class="px-6 py-3">Tgl Pinjam</th>
                            <th class="px-6 py-3">Tgl Kembali</th>
                            <th class="px-6 py-3">Status</th>
                            <th class="px-6 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white text-[#334155]">
                        <?php $no = 1; ?>
                        <?php $__empty_1 = true; $__currentLoopData = $peminjamans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <!-- Dynamic Status helper -->
                        <?php
                            $displayStatus = $item->status;
                            if ($item->status === 'disetujui') {
                                if ($item->alat && $item->alat->status === 'dipinjam') {
                                    $displayStatus = 'dipinjam';
                                } else {
                                    $displayStatus = 'disetujui'; // Siap Diambil
                                }
                            }
                        ?>
                        <!-- Row Filterable via Alpine.js -->
                        <tr x-show="activeTab === 'semua' || activeTab === '<?php echo e($displayStatus); ?>'" class="hover:bg-slate-50/50 transition">
                            <td class="px-6 py-4 text-slate-400 font-medium"><?php echo e($no++); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="font-medium text-[#334155]"><?php echo e($item->user->name ?? 'N/A'); ?></div>
                                <!-- Role Badge -->
                                <div class="mt-1">
                                    <?php if(($item->user->role ?? '') === 'dosen'): ?>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-medium bg-sky-50 text-sky-700">
                                            Dosen
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-medium bg-purple-50 text-purple-700">
                                            Mahasiswa
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap font-medium text-slate-700">
                                <?php echo e($item->alat->nama_alat ?? 'None'); ?>

                            </td>
                            <td class="px-6 py-4 text-slate-500 whitespace-nowrap"><?php echo e($item->tgl_pinjam->format('d M Y')); ?></td>
                            <td class="px-6 py-4 text-slate-500 whitespace-nowrap"><?php echo e($item->tgl_kembali_rencana->format('d M Y')); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php if($item->status === 'pending'): ?>
                                    <span class="inline-flex items-center gap-1 rounded-full bg-amber-50 px-2.5 py-0.5 text-xs font-semibold text-[#F59E0B]">
                                        <span class="w-1 h-1 rounded-full bg-[#F59E0B] animate-pulse"></span>
                                        Pending
                                    </span>
                                <?php elseif($item->status === 'disetujui'): ?>
                                    <?php if($item->alat && $item->alat->status === 'dipinjam'): ?>
                                        <span class="inline-flex items-center gap-1 rounded-full bg-[#E0F2FE] px-2.5 py-0.5 text-xs font-semibold text-[#0EA5E9]">
                                            Dipinjam
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2.5 py-0.5 text-xs font-semibold text-[#10B981]">
                                            Siap Diambil
                                        </span>
                                    <?php endif; ?>
                                <?php elseif($item->status === 'dipinjam'): ?>
                                    <span class="inline-flex items-center gap-1 rounded-full bg-[#E0F2FE] px-2.5 py-0.5 text-xs font-semibold text-[#0EA5E9]">
                                        Dipinjam
                                    </span>
                                <?php elseif($item->status === 'selesai'): ?>
                                    <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-semibold text-slate-500">
                                        Selesai
                                    </span>
                                <?php elseif($item->status === 'menunggu_kembali'): ?>
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-50 px-2.5 py-0.5 text-xs font-semibold text-[#F59E0B] border border-amber-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-[#F59E0B] animate-pulse"></span>
                                        Menunggu Verifikasi
                                    </span>
                                <?php elseif($item->status === 'ditolak'): ?>
                                    <span class="inline-flex items-center gap-1 rounded-full bg-red-50 px-2.5 py-0.5 text-xs font-semibold text-[#EF4444]">
                                        Ditolak
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 text-right space-x-1 whitespace-nowrap">
                                <!-- Detail Button -->
                                <a href="<?php echo e(route('peminjaman.show', $item->id)); ?>" class="text-[#94A3B8] hover:text-[#334155] p-1.5 hover:bg-slate-100 rounded-lg transition inline-flex items-center" title="Detail">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </a>

                                <?php if(Auth::user()->role === 'admin'): ?>
                                    <!-- Konfirmasi (Approve) Button & Tolak Button -->
                                    <?php if($item->status === 'pending'): ?>
                                        <div class="inline-flex items-center gap-1">
                                            <form action="<?php echo e(route('peminjaman.approve', $item->id)); ?>" method="POST" class="inline-block">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('PATCH'); ?>
                                                <button type="submit" class="bg-[#0EA5E9] hover:bg-[#0369A1] text-white text-xs font-semibold rounded-lg px-3 py-1.5 transition active:scale-95 shadow-sm">
                                                    Konfirmasi
                                                </button>
                                            </form>
                                            <form action="<?php echo e(route('peminjaman.reject', $item->id)); ?>" method="POST" class="inline-block">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('PATCH'); ?>
                                                <button type="submit" class="bg-red-500 hover:bg-red-600 text-white text-xs font-semibold rounded-lg px-3 py-1.5 transition active:scale-95 shadow-sm">
                                                    Tolak
                                                </button>
                                            </form>
                                        </div>
                                    <?php endif; ?>

                                    <!-- Serahkan (Handover) Button -->
                                    <?php if($item->status === 'disetujui' && (!$item->alat || $item->alat->status !== 'dipinjam')): ?>
                                        <form action="<?php echo e(route('peminjaman.handover', $item->id)); ?>" method="POST" class="inline-block">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('PATCH'); ?>
                                            <button type="submit" class="bg-[#10B981] hover:bg-emerald-700 text-white text-xs font-semibold rounded-lg px-3 py-1.5 transition active:scale-95 shadow-sm">
                                                Serahkan
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-400">
                                <div class="flex flex-col items-center gap-2">
                                    <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2" />
                                    </svg>
                                    <p class="text-xs">Belum ada data peminjaman.</p>
                                </div>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\UAS Pak Arifin\SIPAP\resources\views/admin/peminjaman/index.blade.php ENDPATH**/ ?>