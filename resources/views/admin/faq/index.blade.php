@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="w-full bg-[#FAF9F7] min-h-[calc(100vh-160px)]" x-data="{
    deleteModalOpen: false,
    deleteActionUrl: '',
    deletePertanyaan: '',
    openDeleteModal(actionUrl, pertanyaan) {
        this.deleteActionUrl = actionUrl;
        this.deletePertanyaan = pertanyaan;
        this.deleteModalOpen = true;
    },
    closeDeleteModal() {
        this.deleteModalOpen = false;
    }
}">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 py-8">
        {{-- Breadcrumb --}}
        <div class="text-[13px] text-[#686563] font-normal mb-4">
            Ruang admin &nbsp;/&nbsp; FAQ &amp; pusat bantuan
        </div>

        {{-- Title & Action Row --}}
        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4 mb-3">
            <div>
                <h1 class="text-[32px] leading-tight font-medium text-[#1C1917]">
                    Manajemen FAQ / Pusat Bantuan
                </h1>
                <p class="text-base text-[#686563] font-normal mt-2">
                    Kelola jawaban, publikasi, dan urutan pertanyaan yang ditampilkan kepada publik.
                </p>
            </div>
            <div class="shrink-0 pt-1">
                <a href="{{ route('admin.faq.create') }}"
                   class="inline-flex items-center justify-center bg-[#16A34A] hover:bg-[#15803d] text-[#1C1917] text-[13px] font-medium rounded-[2px] px-5 py-3 transition whitespace-nowrap">
                    + &nbsp;Tambah FAQ
                </a>
            </div>
        </div>

        {{-- Section Bar --}}
        <div class="mt-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 pb-3 border-b border-[#DDDCD7]">
                <span class="text-sm font-medium text-[#1C1917]">Daftar FAQ</span>
                <span class="text-[13px] text-[#686563]">
                    {{ $totalCount }} FAQ &nbsp;·&nbsp; {{ $publishedCount }} dipublikasikan &nbsp;·&nbsp; {{ $draftCount }} draf
                </span>
            </div>

            {{-- Filter & Search Form --}}
            <form method="GET" action="{{ route('admin.faq.index') }}" class="mt-6 flex flex-col md:flex-row items-stretch md:items-center gap-3">
                {{-- Search Input --}}
                <div class="relative flex-1">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-[#1C1917]">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                            <circle cx="11" cy="11" r="7" />
                            <line x1="16.5" y1="16.5" x2="21" y2="21" />
                        </svg>
                    </div>
                    <input type="text"
                           name="search"
                           value="{{ $search }}"
                           aria-label="Cari pertanyaan atau jawaban"
                           placeholder="Cari pertanyaan atau jawaban…"
                           class="w-full bg-[#FAF9F7] text-sm text-[#1C1917] placeholder-[#686563] pl-11 pr-4 py-3 rounded-lg border border-[#DDDCD7] focus:outline-none focus:border-[#16A34A]">
                </div>

                {{-- Status Dropdown --}}
                <div class="relative">
                    <select name="status"
                            aria-label="Filter status publikasi FAQ"
                            onchange="this.form.submit()"
                            class="w-full md:w-[200px] bg-[#EDECE9] hover:bg-[#e4e2de] text-[13px] font-medium text-[#1C1917] px-4 py-3 rounded-[2px] border-none focus:ring-0 cursor-pointer appearance-none pr-8">
                        <option value="">Status: Semua</option>
                        <option value="published" @selected($statusFilter === 'published')>Status: Dipublikasikan</option>
                        <option value="draft" @selected($statusFilter === 'draft')>Status: Draf</option>
                    </select>
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-[#1C1917]">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                </div>

                {{-- Public Help Center Link --}}
                <a href="{{ route('faq.index') }}"
                   class="inline-flex items-center justify-center md:w-[240px] bg-[#EDECE9] hover:bg-[#e4e2de] text-[13px] font-medium text-[#1C1917] px-4 py-3 rounded-[2px] transition whitespace-nowrap">
                    Lihat pusat bantuan
                </a>
            </form>

            {{-- Table --}}
            <div class="mt-6 border border-[#DDDCD7]/80 rounded-[4px] overflow-hidden bg-[#FAF9F7]">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-[#EDECE9] border-b border-[#DDDCD7]/80">
                                <th scope="col" class="py-3 px-4 text-[11px] font-medium text-[#686563] uppercase tracking-wider w-[8%]">
                                    URUTAN
                                </th>
                                <th scope="col" class="py-3 px-4 text-[11px] font-medium text-[#686563] uppercase tracking-wider w-[44%]">
                                    PERTANYAAN
                                </th>
                                <th scope="col" class="py-3 px-4 text-[11px] font-medium text-[#686563] uppercase tracking-wider w-[15%]">
                                    PUBLIKASI
                                </th>
                                <th scope="col" class="py-3 px-4 text-[11px] font-medium text-[#686563] uppercase tracking-wider w-[13%]">
                                    DIPERBARUI
                                </th>
                                <th scope="col" class="py-3 px-4 text-[11px] font-medium text-[#686563] uppercase tracking-wider w-[20%]">
                                    AKSI
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#DDDCD7]/65">
                            @forelse($faqs as $faq)
                                <tr class="hover:bg-[#EDECE9]/40 transition">
                                    <td class="py-4 px-4 text-[13px] text-[#686563] align-middle">
                                        {{ str_pad((string) $faq->urutan, 2, '0', STR_PAD_LEFT) }}
                                    </td>
                                    <td class="py-4 px-4 text-[14px] font-normal text-[#1C1917] align-middle">
                                        <a href="{{ route('admin.faq.show', $faq) }}" class="hover:underline">
                                            {{ $faq->pertanyaan }}
                                        </a>
                                    </td>
                                    <td class="py-4 px-4 align-middle">
                                        @if($faq->isPublished())
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-[4px] bg-[#E5EFE4] text-[#166534] text-[12px] font-medium">
                                                Dipublikasikan
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-[4px] bg-[#EDECE9] text-[#686563] text-[12px] font-medium">
                                                Draf
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-4 text-[13px] text-[#686563] align-middle whitespace-nowrap">
                                        {{ $faq->updated_at ? $faq->updated_at->translatedFormat('d M Y') : '—' }}
                                    </td>
                                    <td class="py-3 px-4 align-middle whitespace-nowrap">
                                        <div class="inline-flex items-center gap-2">
                                            <a href="{{ route('admin.faq.show', $faq) }}"
                                               class="inline-flex items-center justify-center bg-[#EDECE9] hover:bg-[#e4e2de] text-[#1C1917] text-[13px] font-medium rounded-[2px] px-3.5 py-2.5 transition">
                                                Detail
                                            </a>
                                            <a href="{{ route('admin.faq.edit', $faq) }}"
                                               class="inline-flex items-center justify-center bg-[#EDECE9] hover:bg-[#e4e2de] text-[#1C1917] text-[13px] font-medium rounded-[2px] px-3.5 py-2.5 transition">
                                                Ubah
                                            </a>
                                            <button type="button"
                                                    @click="openDeleteModal('{{ route('admin.faq.destroy', $faq) }}', @js($faq->pertanyaan))"
                                                    class="inline-flex items-center justify-center bg-[#EDECE9] hover:bg-[#e4e2de] text-[#1C1917] text-[13px] font-medium rounded-[2px] px-3.5 py-2.5 transition">
                                                Hapus
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-12 text-center text-sm text-[#686563]">
                                        Belum ada FAQ yang sesuai dengan pencarian atau filter.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Table Footer Info --}}
            <div class="mt-5 flex flex-col sm:flex-row sm:items-center sm:justify-between text-[13px] text-[#686563] gap-2">
                <div>
                    Menampilkan {{ $faqs->count() > 0 ? '1–' . $faqs->count() : '0' }} dari {{ $totalCount }} FAQ
                </div>
                <div>
                    Diurutkan berdasarkan urutan tampil, dari angka terkecil.
                </div>
            </div>

            {{-- Info Note Banner --}}
            <div class="mt-4 bg-[#EDECE9] rounded-[6px] px-4 py-3 text-[13px] text-[#686563]">
                Hanya FAQ berstatus Dipublikasikan yang ditampilkan pada pusat bantuan publik.
            </div>
        </div>
    </div>

    @include('admin.faq._delete-modal')
</div>
@endsection
