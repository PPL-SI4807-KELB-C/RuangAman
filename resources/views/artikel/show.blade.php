@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="mx-auto max-w-6xl px-4 py-8 sm:px-6 sm:py-10">
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <x-button href="{{ route('artikel.index') }}" variant="ghost" size="sm">&larr; Kembali ke Artikel</x-button>
        @can('kelola-artikel')
            <x-button href="{{ route('admin.artikel.edit', $artikel) }}" variant="secondary" size="sm">Kelola Artikel</x-button>
        @endcan
    </div>

    <div class="grid grid-cols-1 gap-8 lg:grid-cols-12">
        <main class="lg:col-span-8">
            <article class="overflow-hidden rounded-xl border border-neutral-200/70 bg-white">
                <header class="border-b border-neutral-200/70 px-6 py-8 sm:px-10 sm:py-10">
                    <div class="flex flex-wrap items-center gap-2">
                        <x-badge variant="brand">{{ $artikel->kategori?->nama_kategori ?? 'Edukasi KBG' }}</x-badge>
                        @if ($artikel->status_publikasi !== \App\Models\Artikel::STATUS_PUBLISHED)
                            <x-badge :variant="$artikel->statusBadgeVariant()">Pratinjau {{ $artikel->statusLabel() }}</x-badge>
                        @endif
                    </div>
                    <h1 class="mt-4 text-3xl font-extrabold tracking-tight text-neutral-900 sm:text-4xl">{{ $artikel->judul }}</h1>
                    <div class="mt-5 flex flex-wrap gap-x-5 gap-y-2 text-xs text-neutral-600">
                        <span>Oleh <span class="font-medium text-neutral-700">{{ $artikel->penulis?->nama_tampilan ?? 'Tim RuangAman' }}</span></span>
                        <time datetime="{{ $artikel->updated_at?->toISOString() }}">Diperbarui {{ $artikel->updated_at?->translatedFormat('d F Y') }}</time>
                    </div>
                </header>
                <div class="px-6 py-8 sm:px-10 sm:py-10">
                    <div class="whitespace-pre-line text-base leading-8 text-neutral-700">{{ $artikel->isi }}</div>
                </div>
            </article>

            <section id="feedback" class="mt-8 scroll-mt-28 rounded-xl border border-neutral-200/70 bg-white p-6 sm:p-8" aria-labelledby="feedback-title">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-brand-700">PBI-12 &middot; Niky</p>
                        <h2 id="feedback-title" class="mt-1 text-xl font-extrabold tracking-tight text-neutral-900">Feedback Artikel</h2>
                        <p class="mt-2 max-w-2xl text-sm leading-relaxed text-neutral-600">Bagikan tanggapan Anda tentang materi edukasi ini. Feedback akan ditampilkan setelah disetujui Admin Kasus. Jangan cantumkan identitas atau detail kasus pribadi.</p>
                    </div>
                    @can('beri-feedback')
                        <x-button href="{{ route('feedback-saya.index') }}" variant="ghost" size="sm">Feedback Saya</x-button>
                    @endcan
                </div>

                @if ($artikel->status_publikasi === \App\Models\Artikel::STATUS_PUBLISHED)
                    @can('beri-feedback')
                        <form method="POST" action="{{ route('feedback.store', $artikel) }}" class="mt-6 space-y-4 border-y border-neutral-200/70 py-6">
                            @csrf
                            <x-textarea
                                name="isi_feedback"
                                label="Tulis Feedback"
                                placeholder="Apa yang Anda pelajari dari artikel ini?"
                                rows="4"
                                helper="Minimal 5 karakter, maksimal 2.000 karakter."
                                :error="$errors->first('isi_feedback')"
                                required
                            />
                            <div class="flex justify-end">
                                <x-button type="submit" variant="primary" size="md">Kirim Feedback</x-button>
                            </div>
                        </form>
                    @else
                        <div class="mt-6 rounded-lg bg-surface px-4 py-4 text-sm text-neutral-700">
                            Ingin memberikan feedback?
                            <a href="{{ route('login') }}" class="font-semibold text-brand-700 hover:underline">Masuk ke akun Pelapor</a>
                            terlebih dahulu.
                        </div>
                    @endcan
                @endif

                <div class="mt-6 space-y-5">
                    @forelse ($feedbackList as $feedback)
                        <article class="border-b border-neutral-200/70 pb-5 last:border-0 last:pb-0">
                            <div class="flex flex-wrap items-center justify-between gap-2">
                                <p class="text-sm font-semibold text-neutral-900">{{ $feedback->namaPengaruhAman() }}</p>
                                <time datetime="{{ $feedback->created_at?->toISOString() }}" class="text-xs text-neutral-600">{{ $feedback->created_at?->translatedFormat('d M Y') }}</time>
                            </div>
                            <p class="mt-2 whitespace-pre-line text-sm leading-relaxed text-neutral-700">{{ $feedback->isi_feedback }}</p>
                        </article>
                    @empty
                        <p class="rounded-lg border border-dashed border-neutral-300 px-4 py-6 text-center text-sm text-neutral-600">Belum ada feedback yang disetujui untuk artikel ini.</p>
                    @endforelse
                </div>

                @if ($feedbackList->hasPages())
                    <div class="mt-6 border-t border-neutral-200/70 pt-4">{{ $feedbackList->links() }}</div>
                @endif
            </section>
        </main>

        <aside class="space-y-6 lg:col-span-4">
            <x-card>
                <h2 class="text-base font-semibold text-neutral-900">Butuh bantuan lebih lanjut?</h2>
                <p class="mt-2 text-sm leading-relaxed text-neutral-600">Artikel ini bersifat edukatif dan tidak menggantikan pendampingan profesional atau proses hukum. Jika Anda dalam bahaya langsung, hubungi layanan darurat setempat.</p>
                <div class="mt-4 space-y-2">
                    <x-button href="{{ route('laporan.create') }}" variant="primary" size="sm" class="w-full">Buat Laporan</x-button>
                    <x-button href="{{ route('kegiatan.index') }}" variant="secondary" size="sm" class="w-full">Kegiatan Edukasi</x-button>
                </div>
            </x-card>

            @if ($artikelTerkait->isNotEmpty())
                <x-card>
                    <h2 class="text-base font-semibold text-neutral-900">Artikel Terkait</h2>
                    <div class="mt-4 space-y-4">
                        @foreach ($artikelTerkait as $item)
                            <div class="border-b border-neutral-200/70 pb-4 last:border-0 last:pb-0">
                                <a href="{{ route('artikel.show', $item) }}" class="text-sm font-semibold leading-snug text-neutral-800 hover:text-brand-700">{{ $item->judul }}</a>
                                <p class="mt-1 text-xs text-neutral-600">{{ $item->updated_at?->translatedFormat('d M Y') }}</p>
                            </div>
                        @endforeach
                    </div>
                </x-card>
            @endif
        </aside>
    </div>
</div>
@endsection
