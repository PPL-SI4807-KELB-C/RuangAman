@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="mx-auto max-w-6xl space-y-6 px-4 py-8 sm:px-6">
    <x-page-header
        eyebrow="PBI-12 · Niky"
        title="Feedback Artikel Saya"
        description="Lihat, ubah, atau hapus feedback yang pernah Anda kirim. Feedback yang diubah akan dimoderasi ulang."
    >
        <x-slot:actions>
            <x-button href="{{ route('artikel.index') }}" variant="secondary" size="md">Jelajahi Artikel</x-button>
        </x-slot:actions>
    </x-page-header>

    <x-card>
        <form method="GET" action="{{ route('feedback-saya.index') }}" class="flex flex-col gap-3 sm:flex-row sm:items-end">
            <div class="sm:w-64">
                <label for="status" class="sr-only">Filter status moderasi</label>
                <select id="status" name="status" class="block w-full rounded-lg border border-line-strong bg-white px-3 py-2.5 text-sm focus:outline-none focus-visible:ring-2 focus-visible:ring-neutral-900">
                    <option value="">Semua Status</option>
                    @foreach ($statusOptions as $value => $label)
                        <option value="{{ $value }}" @selected($selectedStatus === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-2">
                <x-button type="submit" variant="secondary" size="md">Terapkan Filter</x-button>
                @if ($selectedStatus !== '')
                    <x-button href="{{ route('feedback-saya.index') }}" variant="ghost" size="md">Reset</x-button>
                @endif
            </div>
        </form>
    </x-card>

    <div class="overflow-hidden rounded-xl border border-neutral-200/70 bg-white">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-neutral-600">
                <thead class="border-b border-neutral-200/70 bg-surface text-xs uppercase tracking-wider text-neutral-600">
                    <tr>
                        <th scope="col" class="px-6 py-3.5">Artikel</th>
                        <th scope="col" class="px-6 py-3.5">Feedback</th>
                        <th scope="col" class="px-6 py-3.5">Status</th>
                        <th scope="col" class="px-6 py-3.5">Dikirim</th>
                        <th scope="col" class="px-6 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-200/70">
                    @forelse ($feedbackList as $feedback)
                        <tr class="transition hover:bg-neutral-50">
                            <td class="max-w-xs px-6 py-4">
                                @if ($feedback->artikel?->status_publikasi === \App\Models\Artikel::STATUS_PUBLISHED)
                                    <a href="{{ route('artikel.show', $feedback->artikel) }}" class="font-semibold text-neutral-900 hover:text-brand-700">{{ $feedback->artikel->judul }}</a>
                                @else
                                    <span class="font-semibold text-neutral-900">{{ $feedback->artikel?->judul ?? 'Artikel tidak tersedia' }}</span>
                                    <p class="mt-1 text-xs text-neutral-600">Artikel tidak lagi dipublikasikan.</p>
                                @endif
                            </td>
                            <td class="max-w-md px-6 py-4">
                                <p class="line-clamp-3 whitespace-pre-line text-sm leading-relaxed">{{ $feedback->isi_feedback }}</p>
                            </td>
                            <td class="whitespace-nowrap px-6 py-4">
                                <x-badge :variant="$feedback->statusBadgeVariant()">{{ $feedback->statusLabel() }}</x-badge>
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-xs">{{ $feedback->created_at?->translatedFormat('d M Y, H:i') }}</td>
                            <td class="whitespace-nowrap px-6 py-4 text-right">
                                <div class="inline-flex items-center gap-1.5">
                                    <a href="{{ route('feedback-saya.edit', $feedback) }}" class="rounded-lg border border-neutral-300 bg-white px-2.5 py-1.5 text-xs font-medium text-neutral-700 hover:bg-surface">Ubah</a>
                                    <button type="button" @click="$dispatch('open-confirm-modal', {{ Js::from([
                                        'title' => 'Hapus Feedback',
                                        'message' => 'Hapus feedback Anda untuk artikel ini? Tindakan ini tidak dapat dibatalkan.',
                                        'action' => route('feedback-saya.destroy', $feedback),
                                        'method' => 'DELETE',
                                        'confirmText' => 'Ya, Hapus Feedback',
                                        'confirmVariant' => 'danger',
                                    ]) }})" class="rounded-lg border border-red-200 bg-red-50 px-2.5 py-1.5 text-xs font-medium text-safety-rose hover:bg-red-100">Hapus</button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-sm text-neutral-600">Belum ada feedback yang sesuai dengan filter.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($feedbackList->hasPages())
            <div class="border-t border-neutral-200/70 bg-surface px-6 py-4">{{ $feedbackList->links() }}</div>
        @endif
    </div>
</div>
@endsection
