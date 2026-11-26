@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="w-full bg-[#FAF9F7] min-h-[calc(100vh-160px)]" x-data="{
    deleteModalOpen: false,
    deleteActionUrl: '',
    deleteInstansiName: '',
    openDeleteModal(actionUrl, name) {
        this.deleteActionUrl = actionUrl;
        this.deleteInstansiName = name;
        this.deleteModalOpen = true;
    },
    closeDeleteModal() {
        this.deleteModalOpen = false;
    }
}">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 py-8">
        {{-- Breadcrumb --}}
        <div class="text-[13px] text-[#686563] font-normal mb-3">
            Ruang admin &nbsp;/&nbsp; Direktori bantuan
        </div>

        {{-- Title & Action Row --}}
        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4 mb-3">
            <div>
                <h1 class="text-[32px] leading-tight font-medium text-[#1C1917]">
                    Manajemen instansi bantuan
                </h1>
                <p class="text-base text-[#686563] font-normal mt-2">
                    Kelola informasi lembaga yang ditampilkan pada direktori bantuan publik.
                </p>
            </div>
            <div class="shrink-0 pt-1">
                <a href="{{ route('instansi.create') }}"
                   class="inline-flex items-center justify-center bg-[#16A34A] hover:bg-[#15803d] text-[#1C1917] text-sm font-medium rounded-[2px] px-5 py-2.5 transition">
                    + &nbsp;Tambah instansi
                </a>
            </div>
        </div>

        {{-- Section Bar --}}
        <div class="mt-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 pb-3 border-b border-[#DDDCD7]">
                <span class="text-sm font-medium text-[#1C1917]">Daftar instansi</span>
                <span class="text-[13px] text-[#686563]">
                    {{ $totalCount }} instansi &nbsp;·&nbsp; {{ $aktifCount }} aktif &nbsp;·&nbsp; {{ $nonaktifCount }} nonaktif
                </span>
            </div>

            {{-- Filter & Search Form --}}
            <form method="GET" action="{{ route('instansi.index') }}" class="mt-5 flex flex-col md:flex-row items-stretch md:items-center gap-3">
                {{-- Search Input --}}
                <div class="relative flex-1">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-[#1C1917]">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                            <circle cx="11" cy="11" r="7" />
                            <line x1="16.5" y1="16.5" x2="21" y2="21" />
                        </svg>
                    </div>
                    <input type="text"
                           name="search"
                           value="{{ $search }}"
                           aria-label="Cari nama atau alamat instansi"
                           placeholder="Cari nama atau alamat instansi…"
                           class="w-full bg-[#FAF9F7] text-sm text-[#1C1917] placeholder-[#686563] pl-10 pr-4 py-2.5 rounded-lg border border-[#DDDCD7] focus:outline-none focus:border-[#16A34A]">
                </div>

                {{-- Status Dropdown --}}
                <div class="relative">
                    <select name="status"
                            aria-label="Filter status instansi"
                            onchange="this.form.submit()"
                            class="w-full md:w-auto bg-[#EDECE9] hover:bg-[#e4e2de] text-sm font-medium text-[#1C1917] px-4 py-2.5 rounded-[2px] border-none focus:ring-0 cursor-pointer appearance-none pr-8">
                        <option value="">Status: Semua</option>
                        <option value="aktif" @selected($statusFilter === 'aktif')>Status: Aktif</option>
                        <option value="nonaktif" @selected($statusFilter === 'nonaktif')>Status: Nonaktif</option>
                    </select>
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-[#1C1917]">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                </div>

                {{-- Public Directory Link --}}
                <a href="{{ route('instansi.publik') }}"
                   class="inline-flex items-center justify-center bg-[#EDECE9] hover:bg-[#e4e2de] text-sm font-medium text-[#1C1917] px-4 py-2.5 rounded-[2px] transition whitespace-nowrap">
                    Lihat direktori publik
                </a>
            </form>

            {{-- Table --}}
            <div class="mt-6 border border-[#DDDCD7]/80 rounded-[4px] overflow-hidden bg-[#FAF9F7]">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-[#EDECE9] border-b border-[#DDDCD7]/80">
                                <th scope="col" class="py-3 px-4 text-[12px] font-medium text-[#686563] uppercase tracking-wider w-[28%]">
                                    NAMA INSTANSI
                                </th>
                                <th scope="col" class="py-3 px-4 text-[12px] font-medium text-[#686563] uppercase tracking-wider w-[32%]">
                                    ALAMAT
                                </th>
                                <th scope="col" class="py-3 px-4 text-[12px] font-medium text-[#686563] uppercase tracking-wider w-[14%]">
                                    SITUS WEB
                                </th>
                                <th scope="col" class="py-3 px-4 text-[12px] font-medium text-[#686563] uppercase tracking-wider w-[12%]">
                                    STATUS
                                </th>
                                <th scope="col" class="py-3 px-4 text-[12px] font-medium text-[#686563] uppercase tracking-wider w-[14%]">
                                    AKSI
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#DDDCD7]/60">
                            @forelse($instansis as $instansi)
                                <tr class="hover:bg-[#EDECE9]/40 transition">
                                    <td class="py-4 px-4 text-[13px] font-normal text-[#1C1917]">
                                        <a href="{{ route('instansi.show', $instansi) }}" class="hover:underline">
                                            {{ $instansi->nama_instansi }}
                                        </a>
                                    </td>
                                    <td class="py-4 px-4 text-[13px] font-normal text-[#1C1917]">
                                        {{ $instansi->alamat }}
                                    </td>
                                    <td class="py-4 px-4 text-[13px] font-normal text-[#1C1917]">
                                        @if($instansi->website)
                                            <a href="{{ $instansi->website }}" target="_blank" rel="noopener noreferrer" class="hover:underline inline-flex items-center gap-1 text-[#1C1917]">
                                                <span>Tersedia</span>
                                                <span class="sr-only">(tab baru)</span>
                                            </a>
                                        @else
                                            <span class="text-[#1C1917]">—</span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-4">
                                        @if($instansi->status === 'aktif')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-[4px] bg-[#E5EFE4] text-[#166534] text-[13px] font-medium">
                                                Aktif
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-[4px] bg-[#EDECE9] text-[#686563] text-[13px] font-medium">
                                                Nonaktif
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-4 text-[13px] font-normal text-[#1C1917] whitespace-nowrap">
                                        <div class="inline-flex items-center gap-3">
                                            <a href="{{ route('instansi.show', $instansi) }}" class="hover:underline">Detail</a>
                                            <a href="{{ route('instansi.edit', $instansi) }}" class="hover:underline">Ubah</a>
                                            <button type="button"
                                                    @click="openDeleteModal('{{ route('instansi.destroy', $instansi) }}', @js($instansi->nama_instansi))"
                                                    class="hover:underline text-[#1C1917]">
                                                Hapus
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-12 text-center text-sm text-[#686563]">
                                        Tidak ada instansi yang sesuai dengan kriteria pencarian.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Table Footer Info --}}
            <div class="mt-4 flex flex-col sm:flex-row sm:items-center sm:justify-between text-[13px] text-[#686563] gap-2">
                <div>
                    Menampilkan {{ $instansis->count() > 0 ? '1–' . $instansis->count() : '0' }} dari {{ $totalCount }} instansi
                </div>
                <div>
                    Data contoh · Nama, alamat, dan situs bersifat fiktif.
                </div>
            </div>
        </div>
    </div>

    @include('instansi._delete-modal')
</div>
@endsection
