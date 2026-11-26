import Alpine from 'alpinejs';

window.Alpine = Alpine;

/**
 * Keluar Cepat (FR-28) — satu perilaku untuk semua layout.
 * - Klik tombol "Keluar cepat" atau tekan Esc dua kali (dalam 1,2 detik).
 * - Esc sekali tidak memicu apa pun, supaya Esc tetap aman dipakai menutup modal/menu.
 * - Mengakhiri sesi (logout) bila ada, mengosongkan sessionStorage, lalu pindah ke situs netral.
 */
Alpine.data('quickExit', (logoutUrl = '/logout', redirectTo = 'https://www.google.com') => ({
    armed: false,
    armTimer: null,

    handleEscape() {
        if (this.armed) {
            this.exitNow();
            return;
        }

        this.armed = true;
        clearTimeout(this.armTimer);
        this.armTimer = setTimeout(() => {
            this.armed = false;
        }, 1200);
    },

    exitNow() {
        try {
            sessionStorage.clear();
        } catch (e) {
            // abaikan: storage bisa diblokir peramban
        }

        const leave = () => window.location.replace(redirectTo);
        const token = document.querySelector('meta[name=csrf-token]')?.content;

        if (!token) {
            leave();
            return;
        }

        fetch(logoutUrl, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': token, Accept: 'application/json' },
            keepalive: true,
        })
            .catch(() => {})
            .finally(leave);
    },
}));

/** Salin teks ke clipboard dengan umpan balik singkat (dipakai kode laporan). */
Alpine.data('copyText', (text) => ({
    copied: false,

    async copy() {
        try {
            await navigator.clipboard.writeText(text);
            this.copied = true;
            setTimeout(() => {
                this.copied = false;
            }, 2000);
        } catch (e) {
            this.copied = false;
        }
    },
}));

Alpine.start();
