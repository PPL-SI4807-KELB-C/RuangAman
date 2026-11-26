@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="mx-auto max-w-6xl px-4 sm:px-6 py-8 space-y-8" x-data="{
    deleteModalOpen: false,
    deleteType: '',
    deleteTitle: '',
    deleteTarget: '',
    deleteInstansi: '',
    deleteAction: '',
    openDelete(type, target, instansi, action) {
        this.deleteType = type;
        this.deleteTitle = type === 'layanan' ? 'Hapus layanan bantuan?' : 'Hapus kontak bantuan?';
        this.deleteTarget = target;
        this.deleteInstansi = instansi;
        this.deleteAction = action;
        this.deleteModalOpen = true;
    }
}">

    {{-- Breadcrumb --}}
    <nav aria-label="Breadcrumb" class="text-[13px] text-[#686563]">
        <ol class="flex items-center gap-2">
            <li>
                <a href="{{ route('instansi.index') }}" class="hover:text-[#1C1917] hover:underline">Ruang admin</a>
            </li>
            <li aria-hidden="true">/</li>
            <li>
                <a href="{{ route('instansi.index') }}" class="hover:text-[#1C1917] hover:underline">Instansi bantuan</a>
            </li>
            <li aria-hidden="true">/</li>
            <li class="text-[#1C1917] font-medium" aria-current="page">Layanan & kontak</li>
        </ol>
    </nav>

    {{-- Page Header --}}
    <div class="space-y-2">
        <h1 class="text-3xl sm:text-4xl font-medium text-[#1C1917] tracking-tight">Layanan & kontak bantuan</h1>
        <p class="text-base text-[#686563]">Kelola pilihan pendampingan dan informasi kontak untuk setiap instansi.</p>
    </div>

    {{-- Instansi Selector Banner --}}
    <div class="rounded-[12px] bg-[#EDECE9] p-6">
        <div class="text-[12px] font-medium tracking-wider text-[#686563] uppercase mb-2">INSTANSI BANTUAN</div>
        <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-center">
            <div class="md:col-span-7">
                <div class="relative">
                    <select
                        aria-label="Pilih instansi bantuan"
                        class="w-full h-11 appearance-none rounded-[8px] bg-[#FAF9F7] px-4 pr-10 text-[14px] font-medium text-[#1C1917] outline outline-1 -outline-offset-1 outline-[#DDDCD7] focus:outline-2 focus:outline-[#1C1917] cursor-pointer"
                        onchange="if(this.value) window.location.href = this.value"
                    >
                        @foreach ($allInstansi as $item)
                            <option value="{{ route('instansi.layanan.index', $item) }}" @selected($item->instansi_id === $instansi->instansi_id)>
                                {{ $item->nama_instansi }}
                            </option>
                        @endforeach
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-[#1C1917]">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                </div>
            </div>
            <div class="md:col-span-5 md:pl-4 space-y-1">
                <div class="text-[14px] text-[#686563]">{{ $instansi->alamat }}</div>
                <div class="text-[13px] font-medium {{ $instansi->status === 'aktif' ? 'text-[#166534]' : 'text-[#686563]' }}">
                    Status instansi: {{ $instansi->status === 'aktif' ? 'Aktif' : 'Nonaktif' }}
                </div>
            </div>
        </div>
    </div>

    {{-- SECTION 1: LAYANAN BANTUAN --}}
    <section aria-labelledby="judul-layanan-bantuan" class="space-y-4 pt-2">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 id="judul-layanan-bantuan" class="text-2xl font-medium text-[#1C1917]">Layanan bantuan</h2>
                <p class="text-[14px] text-[#686563] mt-1">{{ $layanans->count() }} layanan tersedia pada instansi ini.</p>
            </div>
            <div>
                <a
                    href="{{ route('instansi.layanan.create', $instansi) }}"
                    class="inline-flex items-center justify-center h-11 px-5 rounded-[2px] bg-[#16A34A] text-[14px] font-medium text-[#1C1917] hover:bg-[#15803d] transition-colors"
                >
                    +  Tambah layanan
                </a>
            </div>
        </div>

        {{-- Layanan Table --}}
        <div class="overflow-hidden rounded-[8px] outline outline-[0.5px] outline-[rgba(221,220,215,0.60)] bg-[#FAF9F7]">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="h-11 bg-[#EDECE9] text-[12px] font-medium text-[#686563] outline outline-[0.5px] outline-[rgba(221,220,215,0.60)]">
                            <th scope="col" class="px-4 font-medium tracking-wider w-[280px]">NAMA LAYANAN</th>
                            <th scope="col" class="px-4 font-medium tracking-wider">DESKRIPSI</th>
                            <th scope="col" class="px-4 font-medium tracking-wider text-right w-[190px]">AKSI</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[rgba(221,220,215,0.60)]">
                        @forelse ($layanans as $layanan)
                            <tr class="min-h-[80px] transition-colors hover:bg-neutral-50/70">
                                <td class="px-4 py-5 align-middle text-[14px] font-medium text-[#1C1917] w-[280px]">
                                    {{ $layanan->nama_layanan }}
                                </td>
                                <td class="px-4 py-5 align-middle text-[14px] text-[#1C1917] leading-relaxed">
                                    {{ $layanan->deskripsi }}
                                </td>
                                <td class="px-4 py-5 align-middle text-right whitespace-nowrap w-[190px]">
                                    <div class="flex items-center justify-end gap-2">
                                        <a
                                            href="{{ route('instansi.layanan.edit', [$instansi, $layanan]) }}"
                                            class="inline-flex items-center justify-center h-11 px-4 rounded-[2px] bg-[#EDECE9] text-[14px] font-medium text-[#1C1917] hover:bg-[#DDDCD7] transition-colors"
                                        >
                                            Ubah
                                        </a>
                                        <button
                                            type="button"
                                            @click="openDelete('layanan', '{{ addslashes($layanan->nama_layanan) }}', '{{ addslashes($instansi->nama_instansi) }}', '{{ route('instansi.layanan.destroy', [$instansi, $layanan]) }}')"
                                            class="inline-flex items-center justify-center h-11 px-4 rounded-[2px] bg-[#EDECE9] text-[14px] font-medium text-[#1C1917] hover:bg-[#DDDCD7] transition-colors"
                                        >
                                            Hapus
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-4 py-12 text-center text-[14px] text-[#686563]">
                                    Belum ada layanan yang ditambahkan untuk instansi ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    {{-- SECTION 2: KONTAK BANTUAN --}}
    <section aria-labelledby="judul-kontak-bantuan" class="space-y-4 pt-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 id="judul-kontak-bantuan" class="text-2xl font-medium text-[#1C1917]">Kontak bantuan</h2>
                <p class="text-[14px] text-[#686563] mt-1">{{ $kontaks->count() }} kontak tersedia pada instansi ini.</p>
            </div>
            <div>
                <a
                    href="{{ route('instansi.kontak.create', $instansi) }}"
                    class="inline-flex items-center justify-center h-11 px-5 rounded-[2px] bg-[#16A34A] text-[14px] font-medium text-[#1C1917] hover:bg-[#15803d] transition-colors"
                >
                    +  Tambah kontak
                </a>
            </div>
        </div>

        {{-- Kontak Table --}}
        <div class="overflow-hidden rounded-[8px] outline outline-[0.5px] outline-[rgba(221,220,215,0.60)] bg-[#FAF9F7]">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="h-11 bg-[#EDECE9] text-[12px] font-medium text-[#686563] outline outline-[0.5px] outline-[rgba(221,220,215,0.60)]">
                            <th scope="col" class="px-4 font-medium tracking-wider w-[150px]">JENIS KONTAK</th>
                            <th scope="col" class="px-4 font-medium tracking-wider w-[340px]">NILAI KONTAK</th>
                            <th scope="col" class="px-4 font-medium tracking-wider">KETERANGAN</th>
                            <th scope="col" class="px-4 font-medium tracking-wider text-right w-[190px]">AKSI</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[rgba(221,220,215,0.60)]">
                        @forelse ($kontaks as $kontak)
                            <tr class="h-16 transition-colors hover:bg-neutral-50/70">
                                <td class="px-4 py-4 align-middle text-[14px] text-[#1C1917] w-[150px]">
                                    {{ $kontak->label_jenis_kontak }}
                                </td>
                                <td class="px-4 py-4 align-middle text-[14px] text-[#1C1917] font-medium w-[340px] break-all">
                                    {{ $kontak->nilai_kontak }}
                                </td>
                                <td class="px-4 py-4 align-middle text-[14px] text-[#1C1917]">
                                    {{ $kontak->keterangan ?? '—' }}
                                </td>
                                <td class="px-4 py-4 align-middle text-right whitespace-nowrap w-[190px]">
                                    <div class="flex items-center justify-end gap-2">
                                        <a
                                            href="{{ route('instansi.kontak.edit', [$instansi, $kontak]) }}"
                                            class="inline-flex items-center justify-center h-11 px-4 rounded-[2px] bg-[#EDECE9] text-[14px] font-medium text-[#1C1917] hover:bg-[#DDDCD7] transition-colors"
                                        >
                                            Ubah
                                        </a>
                                        <button
                                            type="button"
                                            @click="openDelete('kontak', '{{ addslashes($kontak->label_jenis_kontak . ' · ' . $kontak->nilai_kontak) }}', '{{ addslashes($instansi->nama_instansi) }}', '{{ route('instansi.kontak.destroy', [$instansi, $kontak]) }}')"
                                            class="inline-flex items-center justify-center h-11 px-4 rounded-[2px] bg-[#EDECE9] text-[14px] font-medium text-[#1C1917] hover:bg-[#DDDCD7] transition-colors"
                                        >
                                            Hapus
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-12 text-center text-[14px] text-[#686563]">
                                    Belum ada kontak yang ditambahkan untuk instansi ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    {{-- Footer note --}}
    <div class="text-[12px] text-[#686563] pt-4">
        Data contoh · Nama, alamat, layanan, nomor, dan situs pada rancangan ini bersifat fiktif.
    </div>

    {{-- FIGMA-EXACT DELETE MODAL (Alpine.js) --}}
    <div
        x-show="deleteModalOpen"
        x-cloak
        class="fixed inset-0 z-50 overflow-y-auto"
        role="dialog"
        aria-modal="true"
        aria-labelledby="modal-title"
    >
        {{-- Backdrop with 0.30 opacity #1C1917 --}}
        <div
            x-show="deleteModalOpen"
            x-transition:enter="ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-[#1C1917]/30 transition-opacity"
            @click="deleteModalOpen = false"
        ></div>

        {{-- Modal Dialog --}}
        <div class="flex min-h-screen items-center justify-center p-4">
            <div
                x-show="deleteModalOpen"
                x-transition:enter="ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="relative w-full max-w-[560px] rounded-[12px] bg-[#FAF9F7] p-8 shadow-2xl space-y-6"
                @click.stop
            >
                <div class="space-y-3">
                    <h3 id="modal-title" class="text-[26px] font-medium leading-[39px] text-[#1C1917]" x-text="deleteTitle"></h3>
                    <div class="text-[18px] font-medium leading-[27px] text-[#1C1917]" x-text="deleteTarget"></div>
                    <div class="text-[13px] text-[#686563]" x-text="deleteInstansi"></div>
                    <p class="text-[16px] leading-[24px] text-[#686563]">
                        <span x-show="deleteType === 'layanan'">Layanan ini akan dihapus dari instansi dan tidak lagi ditampilkan kepada publik.</span>
                        <span x-show="deleteType === 'kontak'">Kontak ini akan dihapus dari instansi dan tidak lagi ditampilkan kepada publik.</span>
                    </p>
                </div>

                {{-- Warning Box --}}
                <div class="rounded-[6px] bg-[#EDECE9] p-4 text-[13px] leading-[19.50px] text-[#686563]">
                    <span x-show="deleteType === 'layanan'">Instansi, kontak, dan layanan lain tidak ikut dihapus.<br/>Tindakan ini tidak dapat dibatalkan.</span>
                    <span x-show="deleteType === 'kontak'">Instansi dan layanan lain tidak ikut dihapus.<br/>Tindakan ini tidak dapat dibatalkan.</span>
                </div>

                {{-- Modal Actions --}}
                <div class="flex items-center justify-end gap-3 pt-2">
                    <button
                        type="button"
                        @click="deleteModalOpen = false"
                        class="h-12 px-6 rounded-[2px] bg-[#EDECE9] text-[14px] font-medium text-[#1C1917] hover:bg-[#DDDCD7] transition-colors"
                    >
                        Batal
                    </button>
                    <form :action="deleteAction" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button
                            type="submit"
                            class="h-12 px-6 rounded-[2px] bg-[#B42318] text-[14px] font-medium text-white hover:bg-[#991b1b] transition-colors"
                        >
                            <span x-show="deleteType === 'layanan'">Ya, hapus layanan</span>
                            <span x-show="deleteType === 'kontak'">Ya, hapus kontak</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
