@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="mx-auto max-w-7xl space-y-6 px-4 py-8 sm:px-6">
    <x-page-header
        eyebrow="PBI-12 · Niky"
        title="Moderasi Feedback Artikel"
        description="Tinjau feedback Pelapor dan setujui atau tolak sebelum ditampilkan pada halaman publik artikel."
    />

    <x-card>
        <form method="GET" action="{{ route('admin.feedback-artikel.index') }}" class="grid grid-cols-1 gap-3 md:grid-cols-12">
            <div class="md:col-span-4">
                <label for="search" class="sr-only">Cari feedback</label>
                <input type="text" id="search" name="search" value="{{ $search }}" placeholder="Cari isi, artikel, atau alias..." class="block w-full rounded-lg border border-line-strong px-3 py-2.5 text-sm focus:outline-none focus-visible:ring-2 focus-visible:ring-neutral-900" />
            </div>
            <div class="md:col-span-3">
                <label for="artikel_id" class="sr-only">Filter artikel</label>
                <select id="artikel_id" name="artikel_id" class="block w-full rounded-lg border border-line-strong bg-white px-3 py-2.5 text-sm focus:outline-none focus-visible:ring-2 focus-visible:ring-neutral-900">
                    <option value="">Semua Artikel</option>
                    @foreach ($artikelOptions as $id => $label)
                        <option value="{{ $id }}" @selected($selectedArtikel === (int) $id)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="md:col-span-2">
                <label for="status" class="sr-only">Filter status</label>
                <select id="status" name="status" class="block w-full rounded-lg border border-line-strong bg-white px-3 py-2.5 text-sm focus:outline-none focus-visible:ring-2 focus-visible:ring-neutral-900">
                    <option value="">Semua Status</option>
                    @foreach ($statusOptions as $value => $label)
                        <option value="{{ $value }}" @selected($selectedStatus === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-2 md:col-span-3">
                <x-button type="submit" variant="secondary" size="md" class="flex-1">Terapkan Filter</x-button>
                @if ($search !== '' || $selectedStatus !== '' || $selectedArtikel > 0)
                    <x-button href="{{ route('admin.feedback-artikel.index') }}" variant="ghost" size="md">Reset</x-button>
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
                        <th scope="col" class="px-6 py-3.5">Pengaruh</th>
                        <th scope="col" class="px-6 py-3.5">Feedback</th>
                        <th scope="col" class="px-6 py-3.5">Status</th>
                        <th scope="col" class="px-6 py-3.5">Tanggal</th>
                        <th scope="col" class="px-6 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-200/70">
                    @forelse ($feedbackList as $feedback)
                        <tr class="transition hover:bg-neutral-50">
                            <td class="max-w-xs px-6 py-4 font-medium text-neutral-900">{{ $feedback->artikel?->judul ?? 'Artikel tidak tersedia' }}</td>
                            <td class="whitespace-nowrap px-6 py-4">{{ $feedback->namaPengaruhAman() }}</td>
                            <td class="max-w-md px-6 py-4">
                                <p class="line-clamp-3 whitespace-pre-line text-sm leading-relaxed">{{ $feedback->isi_feedback }}</p>
                            </td>
                            <td class="whitespace-nowrap px-6 py-4">
                                <x-badge :variant="$feedback->statusBadgeVariant()">{{ $feedback->statusLabel() }}</x-badge>
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-xs">{{ $feedback->created_at?->translatedFormat('d M Y, H:i') }}</td>
                            <td class="whitespace-nowrap px-6 py-4 text-right">
                                <a href="{{ route('admin.feedback-artikel.show', $feedback) }}" class="rounded-lg border border-neutral-300 bg-white px-2.5 py-1.5 text-xs font-medium text-neutral-700 hover:bg-surface">Tinjau</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-sm text-neutral-600">Belum ada feedback yang sesuai dengan pencarian atau filter.</td>
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
