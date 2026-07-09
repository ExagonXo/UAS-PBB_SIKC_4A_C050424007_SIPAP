<?php $__env->startSection('page-title', 'Manajemen Pengguna'); ?>

<?php $__env->startSection('content'); ?>
    <div class="space-y-6">
        <!-- Page Header -->
        <div>
            <h3 class="text-base font-semibold text-[#334155]">Daftar Pengguna</h3>
            <p class="text-xs text-[#94A3B8] mt-1">Semua mahasiswa, dosen, dan admin yang terdaftar di sistem SIPAP.</p>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <?php
                $adminCount = $users->where('role', 'admin')->count();
                $dosenCount = $users->where('role', 'dosen')->count();
                $mahasiswaCount = $users->where('role', 'mahasiswa')->count();
            ?>
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 flex items-center gap-4">
                <div class="w-10 h-10 rounded-xl bg-[#0369A1]/10 flex items-center justify-center">
                    <svg class="w-5 h-5 text-[#0369A1]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-[#0369A1]"><?php echo e($adminCount); ?></p>
                    <p class="text-xs text-[#94A3B8]">Admin</p>
                </div>
            </div>
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 flex items-center gap-4">
                <div class="w-10 h-10 rounded-xl bg-sky-50 flex items-center justify-center">
                    <svg class="w-5 h-5 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-sky-600"><?php echo e($dosenCount); ?></p>
                    <p class="text-xs text-[#94A3B8]">Dosen</p>
                </div>
            </div>
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 flex items-center gap-4">
                <div class="w-10 h-10 rounded-xl bg-purple-50 flex items-center justify-center">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-purple-600"><?php echo e($mahasiswaCount); ?></p>
                    <p class="text-xs text-[#94A3B8]">Mahasiswa</p>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100 text-left text-sm">
                    <thead class="bg-gray-50 text-xs font-semibold text-[#94A3B8] uppercase tracking-wider">
                        <tr>
                            <th class="px-6 py-3 w-14">No</th>
                            <th class="px-6 py-3">Pengguna</th>
                            <th class="px-6 py-3">Email</th>
                            <th class="px-6 py-3">Role</th>
                            <th class="px-6 py-3">Identitas (NIM/NIP)</th>
                            <th class="px-6 py-3">Bergabung</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white text-[#334155]">
                        <?php $__empty_1 = true; $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="px-6 py-4 text-slate-400 font-medium"><?php echo e($index + 1); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full flex items-center justify-center text-white font-bold text-sm flex-shrink-0
                                        <?php echo e($user->role === 'admin' ? 'bg-[#0369A1]' : ($user->role === 'dosen' ? 'bg-sky-500' : 'bg-purple-500')); ?>">
                                        <?php echo e(substr($user->name, 0, 1)); ?>

                                    </div>
                                    <div>
                                        <p class="font-semibold text-[#334155]"><?php echo e($user->name); ?></p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-slate-500"><?php echo e($user->email); ?></td>
                            <td class="px-6 py-4">
                                <?php if($user->role === 'admin'): ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-[#E0F2FE] text-[#0369A1]">Admin</span>
                                <?php elseif($user->role === 'dosen'): ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-sky-50 text-sky-700">Dosen</span>
                                <?php else: ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-purple-50 text-purple-700">Mahasiswa</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 text-slate-500 font-mono text-xs"><?php echo e($user->identifier ?? '-'); ?></td>
                            <td class="px-6 py-4 text-slate-400 text-xs"><?php echo e($user->created_at->format('d M Y')); ?></td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-400">
                                <div class="flex flex-col items-center gap-2">
                                    <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                    </svg>
                                    <p class="text-xs">Belum ada pengguna terdaftar.</p>
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

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\UAS Pak Arifin\SIPAP\resources\views/admin/pengguna/index.blade.php ENDPATH**/ ?>