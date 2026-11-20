@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="mx-auto max-w-6xl px-4 sm:px-6 py-10 space-y-8">
    <!-- Header -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div>
            <div class="flex flex-wrap items-center gap-2">
                <x-badge :variant="$kegiatan->statusBadgeVariant()">{{ $kegiatan->statusLabel() }}</x-badge>
                <x-badge :variant="$kegiatan->metode === 'online' ? 'info' : 'brand'">{{ $kegiatan->metodeLabel() }}</x-badge>
            </div>
            <h1 class="mt-2 text-2xl font-extrabold tracking-tight text-neutral-900">{{ $kegiatan->judul }}</h1>
            <p class="mt-1 text-xs text-neutral-500">
                Narasumber: <span class="font-medium text-neutral-800">{{ $kegiatan->narasumber ?: 'Belum ditentukan' }}</span>
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <x-button href="{{ route('admin.kegiatan-edukasi.edit', $kegiatan) }}" variant="secondary" size="sm">
                Ubah Kegiatan
            </x-button>
            @if ($statistik['total_pendaftar'] > 0)
                @if ($kegiatan->status !== \App\Models\KegiatanEdukasi::STATUS_DIBATALKAN)
                    <button
                        type="button"
                        @click="$dispatch('open-confirm-modal', {{ Js::from([
                            'title' => 'Nonaktifkan Kegiatan Edukasi',
                            'message' => 'Kegiatan ini sudah memiliki ' . $statistik['total_pendaftar'] . ' pendaftar sehingga tidak boleh dihapus permanen. Statusnya akan diubah menjadi Dibatalkan. Lanjutkan?',
                            'action' => route('admin.kegiatan-edukasi.nonaktifkan', $kegiatan),
                            'method' => 'PATCH',
                            'payload' => ['target_status' => \App\Models\KegiatanEdukasi::STATUS_DIBATALKAN],
                            'confirmText' => 'Ya, Nonaktifkan',
                            'confirmVariant' => 'danger',
                        ]) }})"
                        class="text-xs font-medium text-amber-600 hover:underline"
                    >
                        Nonaktifkan
                    </button>
                @endif
            @else
                <button
                    type="button"
                    @click="$dispatch('open-confirm-modal', {{ Js::from([
                        'title' => 'Hapus Kegiatan Edukasi',
                        'message' => 'Apakah Anda yakin ingin menghapus permanen kegiatan “' . $kegiatan->judul . '”?',
                        'action' => route('admin.kegiatan-edukasi.destroy', $kegiatan),
                        'method' => 'DELETE',
                        'confirmText' => 'Ya, Hapus Permanen',
                        'confirmVariant' => 'danger',
                    ]) }})"
                    class="text-xs font-medium text-safety-rose hover:underline"
                >
                    Hapus
                </button>
            @endif
            <x-button href="{{ route('admin.kegiatan-edukasi.index') }}" variant="ghost" size="sm">
                &larr; Kembali
            </x-button>
        </div>
    </div>

    <!-- Ringkasan & Statistik Kuota -->
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <x-card class="lg:col-span-2">
            <h2 class="text-sm font-semibold text-neutral-900 mb-4">Informasi Pelaksanaan</h2>
            <dl class="grid grid-cols-1 gap-4 text-xs sm:grid-cols-2">
                <div>
                    <dt class="text-[11px] font-medium uppercase tracking-wider text-neutral-500">Tanggal &amp; Waktu Mulai</dt>
                    <dd class="mt-1 font-medium text-neutral-900">
                        {{ $kegiatan->tanggal_mulai->translatedFormat('l, d F Y') }} &bull; {{ substr((string) $kegiatan->waktu_mulai, 0, 5) }} WIB
                    </dd>
                </div>
                <div>
                    <dt class="text-[11px] font-medium uppercase tracking-wider text-neutral-500">Tanggal &amp; Waktu Selesai</dt>
                    <dd class="mt-1 font-medium text-neutral-900">
                        @if ($kegiatan->tanggal_selesai || $kegiatan->waktu_selesai)
                            {{ ($kegiatan->tanggal_selesai ?? $kegiatan->tanggal_mulai)->translatedFormat('d F Y') }}
                            {{ $kegiatan->waktu_selesai ? '• ' . substr((string) $kegiatan->waktu_selesai, 0, 5) . ' WIB' : '' }}
                        @else
                            <span class="text-neutral-500">Menyesuaikan sesi</span>
                        @endif
                    </dd>
                </div>
                <div class="sm:col-span-2">
                    <dt class="text-[11px] font-medium uppercase tracking-wider text-neutral-500">
                        {{ $kegiatan->metode === 'online' ? 'Tautan Meeting' : 'Lokasi Pelaksanaan' }}
                    </dt>
                    <dd class="mt-1 text-neutral-800 break-all">
                        {{ $kegiatan->lokasi_atau_tautan ?: 'Belum diisi' }}
                    </dd>
                </div>
                <div class="sm:col-span-2">
                    <dt class="text-[11px] font-medium uppercase tracking-wider text-neutral-500">Deskripsi</dt>
                    <dd class="mt-1 text-neutral-700 whitespace-pre-line leading-relaxed">
                        {{ $kegiatan->deskripsi ?: 'Tidak ada deskripsi tambahan.' }}
                    </dd>
                </div>
            </dl>
        </x-card>

        <x-card>
            <h2 class="text-sm font-semibold text-neutral-900 mb-4">Ringkasan Pendaftar &amp; Kuota</h2>
            <div class="space-y-3 text-xs">
                <div class="flex items-center justify-between rounded bg-white p-3 ring-1 ring-inset ring-neutral-300/70">
                    <span class="text-neutral-600">Total Kuota</span>
                    <span class="font-bold text-neutral-900">{{ $kegiatan->kuota }} orang</span>
                </div>
                <div class="flex items-center justify-between rounded bg-white p-3 ring-1 ring-inset ring-neutral-300/70">
                    <span class="text-neutral-600">Kuota Terisi</span>
                    <span class="font-bold text-neutral-900">{{ $statistik['terisi'] }} orang</span>
                </div>
                <div class="flex items-center justify-between rounded bg-white p-3 ring-1 ring-inset ring-neutral-300/70">
                    <span class="text-neutral-600">Sisa Kuota Tersedia</span>
                    <span class="font-bold text-brand-700">{{ $sisaKuota }} orang</span>
                </div>
                <div class="grid grid-cols-3 gap-2 pt-2 text-center text-xs">
                    <div class="rounded bg-white p-2 ring-1 ring-inset ring-neutral-300/70">
                        <div class="font-bold text-neutral-900">{{ $statistik['hadir'] }}</div>
                        <div class="text-[11px] text-neutral-500">Hadir</div>
                    </div>
                    <div class="rounded bg-white p-2 ring-1 ring-inset ring-neutral-300/70">
                        <div class="font-bold text-neutral-900">{{ $statistik['tidak_hadir'] }}</div>
                        <div class="text-[11px] text-neutral-500">Tdk Hadir</div>
                    </div>
                    <div class="rounded bg-white p-2 ring-1 ring-inset ring-neutral-300/70">
                        <div class="font-bold text-neutral-900">{{ $statistik['dibatalkan'] }}</div>
                        <div class="text-[11px] text-neutral-500">Batal</div>
                    </div>
                </div>
            </div>
        </x-card>
    </div>

    <!-- Daftar Peserta & Verifikasi Kehadiran -->
    <div class="overflow-hidden rounded-xl border border-neutral-200/70 bg-white">
        <div class="flex flex-col gap-2 border-b border-neutral-200 bg-[#f2f2f2] px-6 py-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-sm font-semibold text-neutral-900">Daftar Peserta Terdaftar</h2>
                <p class="text-[11px] text-neutral-500">
                    Identitas resmi dan alamat email peserta dirahasiakan sesuai standar privasi RuangAman.
                </p>
            </div>
            @if (! $sudahDimulai)
                <x-badge variant="warning">Verifikasi dibuka setelah waktu mulai</x-badge>
            @else
                <x-badge variant="success">Verifikasi kehadiran dibuka</x-badge>
            @endif
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-neutral-600">
                <thead class="border-b border-neutral-200 bg-[#f2f2f2] text-[11px] uppercase tracking-wider text-neutral-500">
                    <tr>
                        <th scope="col" class="px-6 py-3 font-medium">Kode Pendaftaran</th>
                        <th scope="col" class="px-6 py-3 font-medium">Alias / Nama Tampilan</th>
                        <th scope="col" class="px-6 py-3 font-medium">Waktu Mendaftar</th>
                        <th scope="col" class="px-6 py-3 text-center font-medium">Status</th>
                        <th scope="col" class="px-6 py-3 text-right font-medium">Verifikasi Kehadiran</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-200/70">
                    @forelse ($kegiatan->pendaftaranKegiatan as $pendaftaran)
                        <tr class="transition-colors hover:bg-[#f2f2f2]/70">
                            <td class="whitespace-nowrap px-6 py-4 font-mono text-xs text-neutral-500">
                                #PDF-{{ str_pad((string) $pendaftaran->pendaftaran_id, 4, '0', STR_PAD_LEFT) }}
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 font-medium text-neutral-900">
                                {{ $pendaftaran->namaPesertaAman() }}
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-xs text-neutral-500">
                                {{ $pendaftaran->created_at?->translatedFormat('d M Y, H:i') }} WIB
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-center">
                                <x-badge :variant="$pendaftaran->statusBadgeVariant()">
                                    {{ $pendaftaran->statusLabel() }}
                                </x-badge>
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-right">
                                @if ($pendaftaran->status_pendaftaran === \App\Models\PendaftaranKegiatan::STATUS_DIBATALKAN)
                                    <span class="text-xs italic text-neutral-500">Dibatalkan peserta</span>
                                @elseif (! in_array($kegiatan->status, [\App\Models\KegiatanEdukasi::STATUS_TAYANG, \App\Models\KegiatanEdukasi::STATUS_SELESAI], true))
                                    <span class="text-xs text-neutral-500">Kegiatan nonaktif</span>
                                @elseif (! $sudahDimulai)
                                    <span class="text-xs text-neutral-500">Menunggu waktu kegiatan</span>
                                @else
                                    <div class="flex items-center justify-end gap-3 text-xs font-medium">
                                        @if ($pendaftaran->status_pendaftaran === \App\Models\PendaftaranKegiatan::STATUS_HADIR)
                                            <span class="text-xs font-semibold text-neutral-900">
                                                Hadir
                                            </span>
                                        @else
                                            <button
                                                type="button"
                                                @click="$dispatch('open-confirm-modal', {{ Js::from([
                                                    'title' => 'Verifikasi Kehadiran Peserta',
                                                    'message' => 'Tandai peserta #PDF-' . str_pad((string) $pendaftaran->pendaftaran_id, 4, '0', STR_PAD_LEFT) . ' (' . $pendaftaran->namaPesertaAman() . ') sebagai HADIR?',
                                                    'action' => route('admin.kegiatan-edukasi.verifikasi-kehadiran', [$kegiatan, $pendaftaran]),
                                                    'method' => 'PATCH',
                                                    'payload' => ['status_pendaftaran' => \App\Models\PendaftaranKegiatan::STATUS_HADIR],
                                                    'confirmText' => 'Ya, Tandai Hadir',
                                                    'confirmVariant' => 'primary',
                                                ]) }})"
                                                class="text-neutral-700 hover:text-neutral-900 hover:underline"
                                            >
                                                Tandai Hadir
                                            </button>
                                        @endif

                                        @if ($pendaftaran->status_pendaftaran === \App\Models\PendaftaranKegiatan::STATUS_TIDAK_HADIR)
                                            <span class="text-xs font-semibold text-neutral-500">
                                                Tidak Hadir
                                            </span>
                                        @else
                                            <button
                                                type="button"
                                                @click="$dispatch('open-confirm-modal', {{ Js::from([
                                                    'title' => 'Verifikasi Ketidakhadiran Peserta',
                                                    'message' => 'Tandai peserta #PDF-' . str_pad((string) $pendaftaran->pendaftaran_id, 4, '0', STR_PAD_LEFT) . ' (' . $pendaftaran->namaPesertaAman() . ') sebagai TIDAK HADIR? Kuota yang ditempati akan dilepaskan.',
                                                    'action' => route('admin.kegiatan-edukasi.verifikasi-kehadiran', [$kegiatan, $pendaftaran]),
                                                    'method' => 'PATCH',
                                                    'payload' => ['status_pendaftaran' => \App\Models\PendaftaranKegiatan::STATUS_TIDAK_HADIR],
                                                    'confirmText' => 'Ya, Tandai Tidak Hadir',
                                                    'confirmVariant' => 'danger',
                                                ]) }})"
                                                class="text-safety-rose hover:underline"
                                            >
                                                Tidak Hadir
                                            </button>
                                        @endif
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-sm text-neutral-500">
                                Belum ada peserta yang mendaftar pada kegiatan ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection