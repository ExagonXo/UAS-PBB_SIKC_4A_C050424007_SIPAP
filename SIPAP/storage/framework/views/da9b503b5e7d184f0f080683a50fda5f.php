<?php $__env->startSection('page-title', 'Manajemen Alat'); ?>

<?php $__env->startSection('content'); ?>
    <div class="space-y-6">
        <!-- Page Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h3 class="text-base font-semibold text-[#334155]">Daftar Alat Presentasi</h3>
                <p class="text-xs text-[#94A3B8] mt-1">Kelola stok, status ketersediaan, dan kondisi fisik alat presentasi kampus.</p>
            </div>
            <?php if(Auth::user()->role === 'admin'): ?>
            <a href="<?php echo e(route('alat.create')); ?>" class="inline-flex items-center justify-center bg-[#0369A1] hover:bg-[#0C4A6E] text-white text-sm font-semibold rounded-lg px-4 py-2 hover:shadow-lg hover:shadow-sky-600/10 active:scale-95 transition-all duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah Alat
            </a>
            <?php endif; ?>
        </div>

        <?php if(session('success')): ?>
        <div class="bg-emerald-50 text-[#10B981] text-xs font-semibold p-4 rounded-xl border border-emerald-100 flex items-center gap-3 shadow-sm">
            <svg class="h-5 w-5 shrink-0 text-[#10B981]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span><?php echo e(session('success')); ?></span>
        </div>
        <?php endif; ?>

        <!-- Filter Row -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <form action="<?php echo e(route('alat.index')); ?>" method="GET" class="flex flex-wrap items-center gap-3">
                <div class="relative flex-1 min-w-[200px]">
                    <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="Cari nama alat..." class="w-full rounded-lg border border-gray-200 bg-slate-50/50 pl-9 pr-4 py-2 text-sm text-[#334155] placeholder-[#94A3B8] focus:border-[#38BDF8] focus:bg-white focus:outline-none focus:ring-1 focus:ring-[#38BDF8] transition">
                    <span class="absolute left-3 top-2.5 text-[#94A3B8]">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </span>
                </div>

                <div class="min-w-[150px]">
                    <select name="status" class="w-full rounded-lg border border-gray-200 bg-slate-50/50 text-sm px-3.5 py-2 text-[#334155] focus:border-[#38BDF8] focus:bg-white focus:outline-none transition">
                        <option value="">Semua Status</option>
                        <option value="tersedia" <?php echo e(request('status') === 'tersedia' ? 'selected' : ''); ?>>Tersedia</option>
                        <option value="kosong" <?php echo e(request('status') === 'kosong' ? 'selected' : ''); ?>>Kosong</option>
                    </select>
                </div>

                <button type="submit" class="bg-slate-100 hover:bg-slate-200 text-[#334155] text-sm font-semibold rounded-lg px-4 py-2 transition duration-150">
                    Filter
                </button>

                <?php if(request()->anyFilled(['search', 'status'])): ?>
                    <a href="<?php echo e(route('alat.index')); ?>" class="text-xs text-[#94A3B8] hover:text-[#0EA5E9] font-medium ml-2">Reset Filter</a>
                <?php endif; ?>
            </form>
        </div>

        <!-- Table Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100 text-left text-sm">
                    <thead class="bg-gray-50 text-xs font-semibold text-[#94A3B8] uppercase tracking-wider">
                        <tr>
                            <th class="px-6 py-3 w-16">No</th>
                            <th class="px-6 py-3">Gambar</th>
                            <th class="px-6 py-3">Nama Alat</th>
                            <th class="px-6 py-3">Merk</th>
                            <th class="px-6 py-3">Stok Total</th>
                            <th class="px-6 py-3">Kondisi</th>
                            <th class="px-6 py-3">Status</th>
                            <?php if(Auth::user()->role === 'admin'): ?>
                            <th class="px-6 py-3 text-right">Aksi</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white text-[#334155]">
                        <?php $__empty_1 = true; $__currentLoopData = $alats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $alat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="px-6 py-4 font-medium text-slate-400">
                                <?php echo e(($alats->currentPage() - 1) * $alats->perPage() + $index + 1); ?>

                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="w-12 h-12 rounded-lg overflow-hidden bg-slate-50 border border-slate-100 flex items-center justify-center">
                                    <?php if($alat->gambar): ?>
                                        <img src="<?php echo e(asset('storage/' . $alat->gambar)); ?>" alt="" class="w-full h-full object-cover">
                                    <?php else: ?>
                                        <svg class="w-6 h-6 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td class="px-6 py-4 font-semibold text-[#334155]">
                                <?php echo e($alat->nama_alat); ?>

                                <p class="text-[10px] text-[#94A3B8] font-normal mt-0.5">ID: <?php echo e($alat->id); ?></p>
                            </td>
                            <td class="px-6 py-4 text-slate-500"><?php echo e($alat->merk ?? '-'); ?></td>
                            <td class="px-6 py-4 font-medium"><?php echo e($alat->stok); ?> Unit</td>
                            <td class="px-6 py-4">
                                <?php if($alat->status === 'rusak'): ?>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-50 text-red-700">
                                        Rusak
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700">
                                        Baik
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4">
                                <?php if($alat->stok > 0): ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-[#E0F2FE] text-[#0EA5E9]">
                                        Tersedia
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-red-50 text-[#EF4444]">
                                        Kosong
                                    </span>
                                <?php endif; ?>
                            </td>
                            <?php if(Auth::user()->role === 'admin'): ?>
                            <td class="px-6 py-4 text-right space-x-2">
                                <a href="<?php echo e(route('alat.edit', $alat->id)); ?>" class="text-[#0EA5E9] hover:text-[#0369A1] transition inline-flex items-center" title="Edit">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                                
                                <form action="<?php echo e(route('alat.destroy', $alat->id)); ?>" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus alat ini?')">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="text-red-400 hover:text-red-600 transition inline-flex items-center" title="Hapus">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                            </td>
                            <?php endif; ?>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-gray-400">
                                <div class="flex flex-col items-center gap-2">
                                    <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                    </svg>
                                    <p class="text-xs">Belum ada alat terdaftar.</p>
                                </div>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination Wrapper -->
            <?php if($alats->hasPages()): ?>
            <div class="px-6 py-4 border-t border-gray-100 bg-white">
                <?php echo e($alats->links()); ?>

            </div>
            <?php endif; ?>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\UAS Pak Arifin\SIPAP\resources\views/admin/alat/index.blade.php ENDPATH**/ ?>