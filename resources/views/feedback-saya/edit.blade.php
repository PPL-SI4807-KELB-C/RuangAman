@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="mx-auto max-w-3xl space-y-6 px-4 py-8 sm:px-6">
    <x-page-header
        eyebrow="PBI-12 · Niky"
        title="Ubah Feedback Artikel"
        description="Perbarui tanggapan Anda. Setelah disimpan, feedback akan kembali menunggu moderasi."
        :back="route('feedback-saya.index')"
        backLabel="Kembali ke Feedback Saya"
    />

    <x-card>
        <div class="mb-6 rounded-lg bg-surface px-4 py-3">
            <p class="text-xs font-semibold uppercase tracking-wider text-neutral-600">Artikel</p>
            <p class="mt-1 text-sm font-semibold text-neutral-900">{{ $feedback->artikel?->judul ?? 'Artikel tidak tersedia' }}</p>
            <div class="mt-2">
                <x-badge :variant="$feedback->statusBadgeVariant()">{{ $feedback->statusLabel() }}</x-badge>
            </div>
        </div>

        <form method="POST" action="{{ route('feedback-saya.update', $feedback) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <x-textarea
                name="isi_feedback"
                label="Isi Feedback"
                :value="$feedback->isi_feedback"
                placeholder="Tulis tanggapan Anda tentang artikel ini"
                rows="6"
                helper="Minimal 5 karakter, maksimal 2.000 karakter. Jangan cantumkan identitas atau detail kasus pribadi."
                :error="$errors->first('isi_feedback')"
                required
            />

            <div class="flex flex-col-reverse gap-3 border-t border-neutral-200/70 pt-5 sm:flex-row sm:justify-end">
                <x-button href="{{ route('feedback-saya.index') }}" variant="secondary" size="md">Batal</x-button>
                <x-button type="submit" variant="primary" size="md">Simpan Perubahan</x-button>
            </div>
        </form>
    </x-card>
</div>
@endsection
