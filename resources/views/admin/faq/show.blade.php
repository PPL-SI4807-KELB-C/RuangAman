@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="w-full bg-[#FAF9F7] min-h-[calc(100vh-160px)]" x-data="{
    deleteModalOpen: false,
    deleteActionUrl: '{{ route('admin.faq.destroy', $faq) }}',
    deletePertanyaan: @js($faq->pertanyaan),
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
            <a href="{{ route('admin.faq.index') }}" class="text-[13px] text-[#686563] hover:text-[#1C1917] font-normal transition inline-flex items-center gap-1.5">
                <span aria-hidden="true">←</span>
                <span>Kembali ke daftar FAQ</span>
            </a>
        </div>

        {{-- Page Header --}}
        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4 mb-8">
            <div>
                <h1 class="text-[36px] font-medium text-[#1C1917] leading-tight">
                    Detail FAQ
                </h1>
                <p class="text-base text-[#686563] font-normal mt-2">
                    Tinjau isi dan pengaturan publikasi pertanyaan.
                </p>
            </div>
            <div class="flex items-center gap-3 pt-1">
                <a href="{{ route('admin.faq.edit', $faq) }}"
                   class="inline-flex items-center justify-center bg-[#16A34A] hover:bg-[#15803d] text-[#1C1917] text-sm font-medium rounded-[2px] px-7 py-3 transition whitespace-nowrap">
                    Ubah FAQ
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
            {{-- Left Column --}}
            <div class="lg:col-span-8">
                <div>
                    <div class="text-[13px] font-medium text-[#686563] tracking-wide uppercase">
                        PERTANYAAN
                    </div>
                    <h2 class="text-[28px] font-medium text-[#1C1917] mt-3 leading-snug">
                        {{ $faq->pertanyaan }}
                    </h2>
                </div>

                <div class="w-full h-px bg-[#DDDCD7] my-8"></div>

                <div>
                    <div class="text-[13px] font-medium text-[#686563] tracking-wide uppercase">
                        JAWABAN
                    </div>
                    <div class="text-[18px] font-normal text-[#1C1917] mt-3 leading-relaxed whitespace-pre-line">{{ $faq->jawaban }}</div>
                </div>

                <div class="w-full h-px bg-[#DDDCD7] my-8"></div>

                <div class="space-y-3 text-[13px] text-[#686563]">
                    <div>
                        ID FAQ: FAQ-{{ str_pad((string) $faq->faq_id, 3, '0', STR_PAD_LEFT) }}
                    </div>
                    <div>
                        Terakhir diperbarui: {{ $faq->updated_at ? $faq->updated_at->translatedFormat('d F Y, H.i') . ' WIB' : '—' }}
                    </div>
                </div>
            </div>

            {{-- Right Sidebar --}}
            <div class="lg:col-span-4 lg:pl-4">
                <div class="bg-[#EDECE9] rounded-[12px] p-6 text-[#1C1917]">
                    <h3 class="text-[20px] font-medium leading-snug">
                        Publikasi &amp; urutan
                    </h3>

                    <div class="mt-4">
                        @if($faq->isPublished())
                            <span class="inline-flex items-center px-3 py-1 rounded-[4px] bg-[#E5EFE4] text-[#166534] text-[12px] font-medium">
                                Dipublikasikan
                            </span>
                        @else
                            <span class="inline-flex items-center px-3 py-1 rounded-[4px] bg-[#FAF9F7] border border-[#DDDCD7] text-[#686563] text-[12px] font-medium">
                                Draf
                            </span>
                        @endif
                    </div>

                    <p class="text-base text-[#686563] font-normal mt-4 leading-relaxed">
                        @if($faq->isPublished())
                            FAQ ini tampil pada pusat bantuan publik.
                        @else
                            FAQ ini berstatus draf dan hanya terlihat oleh admin.
                        @endif
                    </p>

                    <div class="w-full h-px bg-[#DDDCD7] my-6"></div>

                    <div class="text-[12px] font-medium text-[#686563] tracking-wider uppercase">
                        URUTAN TAMPIL
                    </div>
                    <div class="text-[28px] font-medium text-[#1C1917] mt-2">
                        {{ str_pad((string) $faq->urutan, 2, '0', STR_PAD_LEFT) }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('admin.faq._delete-modal')
</div>
@endsection
