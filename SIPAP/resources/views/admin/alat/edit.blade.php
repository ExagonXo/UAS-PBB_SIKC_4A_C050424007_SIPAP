@extends('layouts.admin')

@section('page-title', 'Edit Alat')

@section('content')
    <div class="max-w-4xl mx-auto space-y-6">
        <!-- Page Header -->
        <div class="flex items-center gap-3">
            <a href="{{ route('alat.index') }}" class="p-2 text-[#94A3B8] hover:text-[#334155] hover:bg-slate-100 rounded-lg transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div>
                <h3 class="text-base font-semibold text-[#334155]">Edit Alat</h3>
                <p class="text-xs text-[#94A3B8] mt-1">Ubah spesifikasi alat presentasi ID: {{ $alat->id }}.</p>
            </div>
        </div>

        <!-- Form Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" x-data="{ isSaving: false }">
            <form action="{{ route('alat.update', $alat->id) }}" method="POST" enctype="multipart/form-data" @submit="isSaving = true">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Kiri -->
                    <div class="space-y-4">
                        <div>
                            <label class="text-sm font-medium text-[#334155] mb-1 block">Nama Alat *</label>
                            <input type="text" name="nama_alat" value="{{ old('nama_alat', $alat->nama_alat) }}" required class="border border-gray-200 rounded-lg px-3.5 py-2 text-sm w-full focus:outline-none focus:ring-2 focus:ring-[#38BDF8] focus:border-[#0EA5E9] transition @error('nama_alat') border-red-500 @enderror">
                            @error('nama_alat')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="text-sm font-medium text-[#334155] mb-1 block">Merk / Brand</label>
                            <input type="text" name="merk" value="{{ old('merk', $alat->merk) }}" class="border border-gray-200 rounded-lg px-3.5 py-2 text-sm w-full focus:outline-none focus:ring-2 focus:ring-[#38BDF8] focus:border-[#0EA5E9] transition @error('merk') border-red-500 @enderror">
                            @error('merk')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="text-sm font-medium text-[#334155] mb-1 block">Jumlah Stok *</label>
                            <input type="number" name="stok" value="{{ old('stok', $alat->stok) }}" min="0" required class="border border-gray-200 rounded-lg px-3.5 py-2 text-sm w-full focus:outline-none focus:ring-2 focus:ring-[#38BDF8] focus:border-[#0EA5E9] transition @error('stok') border-red-500 @enderror">
                            @error('stok')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="text-sm font-medium text-[#334155] mb-1 block">Kondisi Alat *</label>
                            <select name="status" class="border border-gray-200 rounded-lg px-3.5 py-2 text-sm w-full focus:outline-none focus:ring-2 focus:ring-[#38BDF8] focus:border-[#0EA5E9] transition @error('status') border-red-500 @enderror">
                                <option value="tersedia" {{ $alat->status === 'tersedia' ? 'selected' : '' }}>Baik / Tersedia</option>
                                <option value="rusak" {{ $alat->status === 'rusak' ? 'selected' : '' }}>Rusak</option>
                                <option value="dipinjam" {{ $alat->status === 'dipinjam' ? 'selected' : '' }}>Dipinjam</option>
                            </select>
                            @error('status')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Kanan -->
                    <div class="space-y-4">
                        <div>
                            <label class="text-sm font-medium text-[#334155] mb-1 block">Deskripsi Alat</label>
                            <textarea name="deskripsi" rows="4" class="border border-gray-200 rounded-lg px-3.5 py-2 text-sm w-full focus:outline-none focus:ring-2 focus:ring-[#38BDF8] focus:border-[#0EA5E9] transition @error('deskripsi') border-red-500 @enderror">{{ old('deskripsi', $alat->deskripsi) }}</textarea>
                            @error('deskripsi')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div x-data="{ imagePreview: '{{ $alat->gambar ? asset('storage/' . $alat->gambar) : '' }}' }">
                            <label class="text-sm font-medium text-[#334155] mb-1 block">Foto Alat</label>
                            
                            <!-- File Input -->
                            <input type="file" name="gambar" accept="image/*" class="border border-gray-200 rounded-lg px-3 py-2 text-sm w-full file:mr-4 file:py-1 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-[#E0F2FE] file:text-[#0369A1] hover:file:bg-[#38BDF8]/20 transition"
                                   @change="
                                       const file = $event.target.files[0];
                                       if (file) {
                                           const reader = new FileReader();
                                           reader.onload = (e) => { imagePreview = e.target.result; };
                                           reader.readAsDataURL(file);
                                       } else {
                                           imagePreview = '';
                                       }
                                   ">

                            <!-- Image Preview Area -->
                            <div class="mt-4 relative w-32 h-32 rounded-xl overflow-hidden border border-slate-100 bg-slate-50" x-show="imagePreview">
                                <img :src="imagePreview" alt="" class="w-full h-full object-cover">
                                <button type="button" class="absolute top-1 right-1 bg-red-500 text-white rounded-full p-1 shadow-md hover:bg-red-600 transition" @click="imagePreview = ''; $event.target.closest('.space-y-4').querySelector('input[type=file]').value = ''">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>

                            @error('gambar')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Action buttons -->
                <div class="mt-8 pt-5 border-t border-slate-100 flex items-center justify-end gap-3">
                    <a href="{{ route('alat.index') }}" class="px-4 py-2 border border-gray-200 rounded-lg text-sm font-semibold text-[#334155] hover:bg-slate-50 transition active:scale-95">
                        Batal
                    </a>
                    
                    <button type="submit" class="bg-[#0369A1] hover:bg-[#0C4A6E] text-white text-sm font-semibold rounded-lg px-6 py-2 transition inline-flex items-center gap-2 active:scale-95 hover:shadow-lg hover:shadow-sky-600/10" :disabled="isSaving">
                        <svg x-show="isSaving" class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span x-text="isSaving ? 'Menyimpan...' : 'Simpan Perubahan'">Simpan Perubahan</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
