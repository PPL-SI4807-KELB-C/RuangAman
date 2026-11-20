@extends('layouts.app')
@section('title', $title)
@section('content')
    <div class="w-full bg-[#FAF9F7] min-h-[calc(100vh-160px)]" x-data="{
        deleteModalOpen: false,
        openDeleteModal() {
            this.deleteModalOpen = true;
        },
        closeDeleteModal() {
            this.deleteModalOpen = false;
        }
    }">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 py-8">
            {{-- Breadcrumb / Back --}}
            <div class="mb-5">
                <a href="{{ route('instansi.index') }}" class="text-[13px] text-[#686563] hover:text-[#1C1917] font-normal transition inline-flex items-center gap-1.5">
                    <span>←</span>
                    <span>Kembali ke daftar instansi</span>
                </a>
            </div>

            {{-- Page Header --}}
            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4 mb-8">
                <div>
                    <h1 class="text-[36px] font-medium text-[#1C1917] leading-tight">
                        {{ $instansi->nama_instansi }}
                    </h1>
                    <p class="text-base text-[#686563] font-normal mt-2">
                        Detail instansi bantuan
                    </p>
                </div>
                <div class="flex items-center gap-3 pt-1">
                    <a href="{{ route('instansi.edit', $instansi) }}" 
                       class="inline-flex items-center justify-center bg-[#16A34A] hover:bg-[#15803d] text-[#1C1917] text-sm font-medium rounded-[2px] px-6 py-3 transition">
                        Ubah instansi
                    </a>
                    <button type="button" 
                            @click="openDeleteModal()"
                            class="inline-flex items-center justify-center bg-[#EDECE9] hover:bg-[#e4e2de] text-[#1C1917] text-sm font-medium rounded-[2px] px-6 py-3 transition">
                        Hapus
                    </button>
                </div>
            </div>

            {{-- Two Column Content --}}
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                {{-- Main Content Column --}}
                <div class="lg:col-span-8">
                    {{-- Alamat --}}
                    <div>
                        <div class="text-[13px] font-medium text-[#686563] tracking-wide uppercase">
                            ALAMAT
                        </div>
                        <div class="text-base text-[#1C1917] font-normal mt-2 leading-relaxed">
                            {{ $instansi->alamat }}
                        </div>
                    </div>

                    <div class="w-full h-px bg-[#DDDCD7] my-6"></div>

                    {{-- Deskripsi --}}
                    <div>
                        <div class="text-[13px] font-medium text-[#686563] tracking-wide uppercase">
                            DESKRIPSI
                        </div>
                        <div class="text-base text-[#1C1917] font-normal mt-2 leading-relaxed">
                            {{ $instansi->deskripsi }}
                        </div>
                    </div>

                    <div class="w-full h-px bg-[#DDDCD7] my-6"></div>

                    {{-- Situs Web --}}
                    <div>
                        <div class="text-[13px] font-medium text-[#686563] tracking-wide uppercase">
                            SITUS WEB
                        </div>
                        @if($instansi->website)
                            <div class="mt-2">
                                <a href="{{ $instansi->website }}" target="_blank" rel="noopener noreferrer" class="text-base text-[#1C1917] hover:underline inline-flex items-center gap-1.5 font-normal">
                                    <span>{{ $instansi->website }}</span>
                                    <span>↗</span>
                                </a>
                                <p class="text-[13px] text-[#686563] mt-1">
                                    Membuka situs instansi di tab baru.
                                </p>
                            </div>
                        @else
                            <div class="text-base text-[#686563] mt-2">—</div>
                        @endif
                    </div>

                    <div class="w-full h-px bg-[#DDDCD7] my-6"></div>

                    {{-- Meta Info Row --}}
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 text-[13px] text-[#686563]">
                        <div>
                            ID instansi: INS-{{ str_pad($instansi->instansi_id, 3, '0', STR_PAD_LEFT) }}
                        </div>
                        <div>
                            Dibuat: {{ $instansi->created_at ? $instansi->created_at->translatedFormat('d F Y') : '-' }}
                        </div>
                    </div>
                    <div class="text-[13px] text-[#686563] mt-2">
                        Terakhir diperbarui: {{ $instansi->updated_at ? $instansi->updated_at->translatedFormat('d F Y, H.i') . ' WIB' : '-' }}
                    </div>
                </div>

                {{-- Sidebar Column --}}
                <div class="lg:col-span-4 lg:pl-4">
                    <div class="bg-[#EDECE9] rounded-[12px] p-6 text-[#1C1917]">
                        <h2 class="text-[20px] font-medium leading-snug">
                            Status instansi
                        </h2>
                        <div class="mt-3">
                            @if($instansi->status === 'aktif')
                                <span class="inline-flex items-center px-3 py-1 rounded-[4px] bg-[#E5EFE4] text-[#166534] text-[13px] font-medium">
                                    Aktif
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-[4px] bg-[#EDECE9] border border-[#DDDCD7] text-[#686563] text-[13px] font-medium">
                                    Nonaktif
                                </span>
                            @endif
                        </div>
                        <p class="text-base text-[#686563] font-normal mt-3 leading-relaxed">
                            @if($instansi->status === 'aktif')
                                Instansi ini tampil di direktori publik dan dapat dilihat tanpa login.
                            @else
                                Instansi ini hanya terlihat oleh admin dan disembunyikan dari direktori publik.
                            @endif
                        </p>
                    </div>

                    <p class="text-[12px] text-[#686563] mt-4 px-1">
                        Data contoh · Informasi instansi bersifat fiktif.
                    </p>
                </div>
            </div>
        </div>

        {{-- Modal Hapus (Figma Exact) --}}
        <div x-show="deleteModalOpen" 
             x-transition.opacity
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-[#1C1917]/40" 
             x-cloak>
            <div class="bg-[#FAF9F7] rounded-[12px] w-full max-w-[560px] p-8 shadow-xl relative"
                 @click.away="closeDeleteModal()"
                 @keydown.escape.window="closeDeleteModal()">
                <h2 class="text-[26px] font-medium text-[#1C1917] leading-tight">
                    Hapus instansi bantuan?
                </h2>
                <div class="text-[18px] font-medium text-[#1C1917] mt-3">{{ $instansi->nama_instansi }}</div>
                <p class="text-base text-[#686563] font-normal mt-2 leading-relaxed">
                    Instansi akan dihapus dari daftar admin dan direktori publik. Tindakan ini tidak dapat dibatalkan.
                </p>

                <div class="bg-[#EDECE9] rounded-[6px] p-4 text-[13px] text-[#686563] mt-5 leading-relaxed">
                    Hanya ingin menyembunyikannya dari publik?<br/>
                    Ubah status instansi menjadi Nonaktif.
                </div>

                <div class="flex items-center justify-end gap-3 mt-8">
                    <button type="button" 
                            @click="closeDeleteModal()"
                            class="bg-[#EDECE9] hover:bg-[#e4e2de] text-[#1C1917] text-sm font-medium rounded-[2px] px-6 py-3 transition">
                        Batal
                    </button>
                    <form action="{{ route('instansi.destroy', $instansi) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="bg-[#B42318] hover:bg-[#991b1b] text-white text-sm font-medium rounded-[2px] px-6 py-3 transition">
                            Ya, hapus instansi
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
