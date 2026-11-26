@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="mx-auto max-w-4xl space-y-6 px-4 py-8 sm:px-6">
    <x-page-header
        eyebrow="PBI-12 · Niky"
        title="Detail Feedback Artikel"
        description="Tinjau isi feedback dan tentukan status moderasinya."
        :back="route('admin.feedback-artikel.index')"
        backLabel="Kembali ke Daftar Feedback"
    />

    <x-card>
        <dl class="grid grid-cols-1 gap-5 border-b border-neutral-200/70 pb-6 sm:grid-cols-2">
            <div>
                <dt class="text-xs font-semibold uppercase tracking-wider text-neutral-600">Artikel</dt>
                <dd class="mt-1 text-sm font-medium text-neutral-900">
                    @if ($feedback->artikel?->status_publikasi === \App\Models\Artikel::STATUS_PUBLISHED)
                        <a href="{{ route('artikel.show', $feedback->artikel) }}" class="hover:text-brand-700 hover:underline">{{ $feedback->artikel->judul }}</a>
                    @else
                        {{ $feedback->artikel?->judul ?? 'Artikel tidak tersedia' }}
                    @endif
                </dd>
            </div>
            <div>
                <dt class="text-xs font-semibold uppercase tracking-wider text-neutral-600">Pengaruh</dt>
                <dd class="mt-1 text-sm font-medium text-neutral-900">{{ $feedback->namaPengaruhAman() }}</dd>
            </div>
            <div>
                <dt class="text-xs font-semibold uppercase tracking-wider text-neutral-600">Dikirim</dt>
                <dd class="mt-1 text-sm text-neutral-700">{{ $feedback->created_at?->translatedFormat('d F Y, H:i') }}</dd>
            </div>
            <div>
                <dt class="text-xs font-semibold uppercase tracking-wider text-neutral-600">Status Saat Ini</dt>
                <dd class="mt-1"><x-badge :variant="$feedback->statusBadgeVariant()">{{ $feedback->statusLabel() }}</x-badge></dd>
            </div>
        </dl>

        <div class="py-6">
            <h2 class="text-sm font-semibold text-neutral-900">Isi Feedback</h2>
            <p class="mt-3 whitespace-pre-line text-sm leading-7 text-neutral-700">{{ $feedback->isi_feedback }}</p>
        </div>

        <form method="POST" action="{{ route('admin.feedback-artikel.moderasi', $feedback) }}" class="border-t border-neutral-200/70 pt-6">
            @csrf
            @method('PATCH')

            <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div class="sm:w-72">
                    <label for="status_moderasi" class="mb-1.5 block text-xs font-semibold text-neutral-800">Status Moderasi</label>
                    <select id="status_moderasi" name="status_moderasi" class="block w-full rounded-lg border border-line-strong bg-white px-3 py-2.5 text-sm focus:outline-none focus-visible:ring-2 focus-visible:ring-neutral-900">
                        @foreach ($statusOptions as $value => $label)
                            <option value="{{ $value }}" @selected($feedback->status_moderasi === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('status_moderasi')
                        <p class="mt-1.5 text-xs text-safety-rose">{{ $message }}</p>
                    @enderror
                    <p class="mt-2 text-xs leading-relaxed text-neutral-600">Hanya feedback berstatus Disetujui yang ditampilkan kepada publik.</p>
                </div>
                <x-button type="submit" variant="primary" size="md">Simpan Moderasi</x-button>
            </div>
        </form>
    </x-card>
</div>
@endsection
