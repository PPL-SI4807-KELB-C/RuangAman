@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="w-full bg-[#FAF9F7] min-h-[calc(100vh-160px)]" x-data="{
    deleteModalOpen: false,
    deleteActionUrl: '{{ route('instansi.destroy', $instansi) }}',
    deleteInstansiName: @js($instansi->nama_instansi),
    openDeleteModal() {
        this.deleteModalOpen = true;
    },
    closeDeleteModal() {
        this.deleteModalOpen = false;
    }
}">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 py-8">
        {{-- Back Link --}}
        <div class="mb-5">
            <a href="{{ route('instansi.index') }}" class="text-[13px] text-[#686563] hover:text-[#1C1917] font-normal transition inline-flex items-center gap-1.5">
                <span aria-hidden="true">←</span>
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
                    <div class="text-base text-[#1C1917] font-normal mt-2 leading-relaxed whitespace-pre-line">{{ $instansi->deskripsi }}</div>
                </div>

                <div class="w-full h-px bg-[#DDDCD7] my-6"></div>

                {{-- Situs Web --}}
                <div>
                    <div class="text-[13px] font-medium text-[#686563] tracking-wide uppercase">
                        SITUS WEB
                    </div>
                    @if($instansi->website)
                        <div class="mt-2">
                            <a href="{{ $instansi->website }}" target="_blank" rel="noopener noreferrer" class="text-base text-[#1C1917] hover:underline inline-flex items-center gap-1.5 font-normal break-all">
                                <span>{{ $instansi->website }}</span>
                                <span aria-hidden="true">↗</span>
                                <span class="sr-only">(tab baru)</span>
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

                {{-- Tautan Cepat ke Manajemen Layanan & Kontak (PBI-08) --}}
                @if (Route::has('instansi.layanan.index'))
                    <div class="w-full h-px bg-[#DDDCD7] my-6"></div>
                    <div class="rounded-[12px] bg-[#EDECE9] p-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div>
                            <div class="text-[14px] font-medium text-[#1C1917]">Layanan & kontak bantuan</div>
                            <p class="text-[13px] text-[#686563] mt-0.5">
                                {{ $instansi->layananBantuan->count() }} layanan · {{ $instansi->kontakBantuan->count() }} kontak terdaftar pada instansi ini.
                            </p>
                        </div>
                        <a href="{{ route('instansi.layanan.index', $instansi) }}"
                           class="inline-flex items-center justify-center bg-[#FAF9F7] hover:bg-white border border-[#DDDCD7] text-[#1C1917] text-[13px] font-medium rounded-[2px] px-4 py-2.5 transition whitespace-nowrap">
                            Kelola layanan &amp; kontak →
                        </a>
                    </div>
                @endif
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

    @include('instansi._delete-modal')
</div>
@endsection
