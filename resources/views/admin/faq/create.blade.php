@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="w-full bg-[#FAF9F7] min-h-[calc(100vh-160px)]">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 py-8">
        {{-- Back Link --}}
        <div class="mb-5">
            <a href="{{ route('admin.faq.index') }}" class="text-[13px] text-[#686563] hover:text-[#1C1917] font-normal transition inline-flex items-center gap-1.5">
                <span aria-hidden="true">←</span>
                <span>Kembali ke daftar FAQ</span>
            </a>
        </div>

        {{-- Page Header --}}
        <div class="mb-8">
            <h1 class="text-[36px] font-medium text-[#1C1917] leading-tight">
                Tambah FAQ
            </h1>
            <p class="text-base text-[#686563] font-normal mt-2">
                Lengkapi pertanyaan dan jawaban. Kolom bertanda * wajib diisi.
            </p>
        </div>

        {{-- Two Column Content --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            <div class="lg:col-span-8">
                <form action="{{ route('admin.faq.store') }}" method="POST" class="space-y-6">
                    @csrf
                    @include('admin.faq._form', ['faq' => $faq, 'submitLabel' => 'Simpan FAQ'])
                </form>
            </div>

            @include('admin.faq._sidebar')
        </div>
    </div>
</div>
@endsection
