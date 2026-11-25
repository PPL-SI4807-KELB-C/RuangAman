@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="w-full">

    {{-- Hero & pencarian --}}
    <section class="w-full bg-white pt-14 pb-10 sm:pt-16">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 grid grid-cols-1 lg:grid-cols-12 gap-10 items-start">
          <div class="lg:col-span-8">
            <p class="text-xs font-medium uppercase tracking-wider text-neutral-600">
                Edukasi &amp; pencegahan KBG
            </p>
            <h1 class="mt-3 text-3xl sm:text-4xl font-extrabold text-neutral-900 tracking-tight leading-[1.15]">
                Glosarium Istilah KBG
            </h1>
            <p class="mt-3 text-sm text-neutral-600 leading-relaxed max-w-xl">
                Pengertian istilah yang sering muncul dalam pelaporan dan pendampingan kasus kekerasan berbasis gender.
            </p>

            <form method="GET" action="{{ route('glosarium.index') }}" class="mt-8 flex max-w-xl flex-col gap-3 sm:flex-row sm:items-center">
                <x-search-input
                    class="w-full"
                    :value="$search"
                    label="Cari istilah"
                    placeholder="Cari istilah, definisi, atau kategori…"
                />
                <div class="flex shrink-0 gap-3">
                    <x-button type="submit" variant="primary" size="md">Cari</x-button>
                    @if ($search !== '')
                        <x-button href="{{ route('glosarium.index') }}" variant="ghost" size="md">Reset</x-button>
                    @endif
                </div>
            </form>

            @if ($search !== '')
                <p class="mt-4 text-xs text-neutral-600">
                    {{ $glosariumList->total() }} istilah cocok dengan pencarian "{{ $search }}".
                </p>
            @endif
          </div>
        </div>
    </section>

    {{-- Daftar istilah per huruf --}}
    <section class="w-full bg-surface border-t border-neutral-200/70 py-14 sm:py-16">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 grid grid-cols-1 lg:grid-cols-12 gap-10 items-start">
          <div class="lg:col-span-8 space-y-12">
            @forelse ($grupHuruf as $huruf => $istilahPerHuruf)
                <div class="space-y-5" aria-labelledby="huruf-{{ $huruf }}">
                    <h2 id="huruf-{{ $huruf }}" class="text-xs font-semibold uppercase tracking-wider text-neutral-600">
                        {{ $huruf }}
                    </h2>

                    <dl class="space-y-5">
                        @foreach ($istilahPerHuruf as $item)
                            <div class="border-b border-neutral-300/60 pb-5 last:border-0 last:pb-0">
                                <dt class="flex flex-wrap items-center gap-x-2.5 gap-y-1 text-sm font-semibold text-neutral-900">
                                    <span>{{ $item->istilah }}</span>
                                    @if ($item->kategori)
                                        <x-badge variant="slate">{{ $item->kategori }}</x-badge>
                                    @endif
                                </dt>
                                <dd class="mt-1.5 whitespace-pre-line text-xs leading-relaxed text-neutral-600">
                                    {{ $item->definisi }}
                                </dd>
                            </div>
                        @endforeach
                    </dl>
                </div>
            @empty
                <div class="rounded-xl border border-dashed border-neutral-300 bg-white p-8 text-center">
                    <p class="text-xs text-neutral-600 leading-relaxed">
                        Belum ada istilah yang cocok. Coba kata lain, atau cek kembali nanti karena glosarium terus dilengkapi.
                    </p>
                </div>
            @endforelse

            @if ($glosariumList->hasPages())
                <div class="border-t border-neutral-300/60 pt-6">
                    {{ $glosariumList->links() }}
                </div>
            @endif
          </div>
        </div>
    </section>

</div>
@endsection
