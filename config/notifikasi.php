<?php

return [

    /*
    |--------------------------------------------------------------------------
    | PBI-18: Pengiriman Notifikasi melalui Email
    |--------------------------------------------------------------------------
    |
    | Notifikasi in-app selalu disimpan ke tabel `notifikasi`. Bila opsi ini
    | aktif, salinan pemberitahuan singkat juga dikirim ke email pengguna.
    | Isi email sengaja dibuat generik agar tidak membocorkan detail kasus.
    |
    */

    'kirim_email' => (bool) env('NOTIFIKASI_KIRIM_EMAIL', false),

    /*
    | Jumlah notifikasi per halaman pada daftar notifikasi pengguna.
    */
    'per_halaman' => 10,

];
