@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="w-full bg-[#FAF9F7] min-h-[calc(100vh-160px)]">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 py-8">
        {{-- Back Link --}}
        <div class="mb-5">
            <a href="{{ route('instansi.publik') }}" class="text-[13px] text-[#686563] hover:text-[#1C1917] font-normal transition inline-flex items-center gap-1.5">
                <span aria-hidden="true">←</span>
                <span>Kembali ke direktori bantuan</span>
            </a>
        </div>

        {{-- Page Header --}}
        <div class="mb-8">
            <h1 class="text-[36px] sm:text-[44px] font-medium text-[#1C1917] leading-tight">
                {{ $instansi->nama_instansi }}
            </h1>
            <p class="text-base text-[#686563] font-normal mt-3 leading-relaxed max-w-4xl whitespace-pre-line">{{ $instansi->deskripsi }}</p>
        </div>

        {{-- Address Banner --}}
        <div class="bg-[#EDECE9] rounded-[12px] p-6 mb-10">
            <div class="text-[12px] font-medium text-[#686563] tracking-wider uppercase">
                ALAMAT INSTANSI
            </div>
            <div class="text-[18px] font-medium text-[#1C1917] mt-1.5">
                {{ $instansi->alamat }}
            </div>
        </div>

        {{-- Two Column Content --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-start">
            {{-- Left Column: Services & Safety Note --}}
            <div class="lg:col-span-8">
                <div>
                    <h2 class="text-[28px] font-medium text-[#1C1917] leading-snug">
                        Layanan yang tersedia
                    </h2>
                    <p class="text-base text-[#686563] font-normal mt-1.5">
                        Pilih dukungan sesuai kebutuhan dan kesiapan Anda.
                    </p>
                </div>

                <div class="mt-8 divide-y divide-[#DDDCD7]">
                    @forelse ($instansi->layananBantuan as $layanan)
                        <div class="py-7 first:pt-2 flex items-start gap-6">
                            <div class="w-10 shrink-0 pt-1 text-[13px] font-medium text-[#686563]">
                                {{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}
                            </div>
                            <div class="space-y-2">
                                <h3 class="text-[22px] font-medium text-[#1C1917] leading-snug">
                                    {{ $layanan->nama_layanan }}
                                </h3>
                                @if ($layanan->deskripsi)
                                    <p class="text-base text-[#686563] font-normal leading-relaxed">
                                        {{ $layanan->deskripsi }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="py-8 text-sm text-[#686563]">
                            Daftar layanan untuk instansi ini sedang diperbarui. Silakan hubungi kontak instansi untuk informasi layanan terbaru.
                        </div>
                    @endforelse
                </div>

                {{-- Safety Note Card --}}
                <div class="bg-[#EDECE9] rounded-[12px] p-6 mt-10">
                    <h3 class="text-[18px] font-medium text-[#1C1917]">
                        Utamakan kenyamanan dan keselamatan Anda.
                    </h3>
                    <p class="text-[14px] text-[#686563] font-normal mt-2 leading-relaxed">
                        Gunakan perangkat yang aman. Anda dapat menanyakan informasi awal sebelum memutuskan untuk melanjutkan pendampingan.
                    </p>
                </div>
            </div>

            {{-- Right Column: Contact Sidebar --}}
            <div class="lg:col-span-4">
                <div class="bg-[#EDECE9] rounded-[12px] p-6 text-[#1C1917]">
                    <h2 class="text-[24px] font-medium leading-snug">
                        Hubungi instansi
                    </h2>
                    <p class="text-[14px] text-[#686563] font-normal mt-1.5">
                        Pilih cara yang nyaman bagi Anda.
                    </p>

                    @php
                        $hasWebsiteContact = $instansi->kontakBantuan->contains('jenis_kontak', 'website');
                    @endphp

                    <div class="mt-6 space-y-5">
                        @foreach ($instansi->kontakBantuan as $kontak)
                            @if (! $loop->first)
                                <div class="w-full h-px bg-[#DDDCD7]"></div>
                            @endif

                            <div class="space-y-2">
                                <div class="text-[12px] font-medium text-[#686563] uppercase tracking-wider">
                                    {{ match ($kontak->jenis_kontak) {
                                        'email'    => 'EMAIL',
                                        'telepon'  => 'TELEPON',
                                        'whatsapp' => 'WHATSAPP',
                                        'hotline'  => 'HOTLINE',
                                        'website'  => 'SITUS WEB',
                                        default    => strtoupper($kontak->label_jenis_kontak),
                                    } }}
                                </div>

                                @if ($kontak->jenis_kontak === 'email')
                                    <div class="text-[14px] font-medium text-[#1C1917] break-all">{{ $kontak->nilai_kontak }}</div>
                                    @if ($kontak->keterangan)
                                        <div class="text-[13px] text-[#686563]">{{ $kontak->keterangan }}</div>
                                    @endif
                                    <div class="pt-1">
                                        <a href="mailto:{{ $kontak->nilai_kontak }}"
                                           class="w-full inline-flex items-center justify-center bg-[#16A34A] hover:bg-[#15803d] text-[#1C1917] text-[14px] font-medium rounded-[2px] px-4 py-2.5 transition">
                                            Kirim email &nbsp;↗
                                        </a>
                                    </div>
                                @elseif (in_array($kontak->jenis_kontak, ['telepon', 'hotline'], true))
                                    <div class="text-[18px] font-medium text-[#1C1917]">{{ $kontak->nilai_kontak }}</div>
                                    @if ($kontak->keterangan)
                                        <div class="text-[13px] text-[#686563]">{{ $kontak->keterangan }}</div>
                                    @endif
                                    <div class="pt-1">
                                        <a href="tel:{{ preg_replace('/[^0-9+]/', '', $kontak->nilai_kontak) }}"
                                           class="w-full inline-flex items-center justify-center bg-[#FAF9F7] hover:bg-white border border-[#DDDCD7] text-[#1C1917] text-[14px] font-medium rounded-[2px] px-4 py-2.5 transition">
                                            Hubungi lewat telepon
                                        </a>
                                    </div>
                                @elseif ($kontak->jenis_kontak === 'whatsapp')
                                    <div class="text-[18px] font-medium text-[#1C1917]">{{ $kontak->nilai_kontak }}</div>
                                    @if ($kontak->keterangan)
                                        <div class="text-[13px] text-[#686563]">{{ $kontak->keterangan }}</div>
                                    @endif
                                    <div class="pt-1">
                                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $kontak->nilai_kontak) }}" target="_blank" rel="noopener noreferrer"
                                           class="w-full inline-flex items-center justify-center bg-[#FAF9F7] hover:bg-white border border-[#DDDCD7] text-[#1C1917] text-[14px] font-medium rounded-[2px] px-4 py-2.5 transition">
                                            Hubungi lewat WhatsApp &nbsp;↗
                                        </a>
                                    </div>
                                @elseif ($kontak->jenis_kontak === 'website')
                                    <div>
                                        <a href="{{ $kontak->nilai_kontak }}" target="_blank" rel="noopener noreferrer"
                                           class="text-[14px] font-medium text-[#1C1917] hover:underline break-all inline-flex items-center gap-1">
                                            <span>{{ $kontak->nilai_kontak }}</span>
                                            <span aria-hidden="true">↗</span>
                                        </a>
                                    </div>
                                    @if ($kontak->keterangan)
                                        <div class="text-[13px] text-[#686563]">{{ $kontak->keterangan }}</div>
                                    @endif
                                @endif
                            </div>
                        @endforeach

                        @if ($instansi->website && ! $hasWebsiteContact)
                            @if ($instansi->kontakBantuan->isNotEmpty())
                                <div class="w-full h-px bg-[#DDDCD7]"></div>
                            @endif
                            <div class="space-y-1.5">
                                <div class="text-[12px] font-medium text-[#686563] uppercase tracking-wider">SITUS WEB</div>
                                <div>
                                    <a href="{{ $instansi->website }}" target="_blank" rel="noopener noreferrer"
                                       class="text-[14px] font-medium text-[#1C1917] hover:underline break-all inline-flex items-center gap-1">
                                        <span>{{ $instansi->website }}</span>
                                        <span aria-hidden="true">↗</span>
                                    </a>
                                </div>
                                <div class="text-[13px] text-[#686563]">Informasi resmi instansi</div>
                            </div>
                        @endif

                        @if ($instansi->kontakBantuan->isEmpty() && ! $instansi->website)
                            <p class="text-[13px] text-[#686563]">
                                Kontak langsung belum tersedia untuk instansi ini.
                            </p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Footer Disclaimers --}}
        <div class="mt-12 pt-6 border-t border-[#DDDCD7]/60 space-y-2">
            <p class="text-[13px] text-[#686563] font-normal">
                Bukan layanan darurat. Jika ada ancaman langsung, cari tempat aman dan bantuan setempat.
            </p>
            <p class="text-[12px] text-[#686563] font-normal">
                Data contoh · Nama, alamat, layanan, nomor, dan situs pada rancangan ini bersifat fiktif.
            </p>
        </div>
    </div>
</div>
@endsection
