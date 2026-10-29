<!-- Tombol Keluar Cepat (FR-28). Layout wajib memuat <meta name="csrf-token" content="{{ csrf_token() }}">. -->
@props([
    'logoutUrl' => '/logout',
    'redirectTo' => 'https://www.google.com',
])

<div
    x-data="{
        armed: false,
        armTimer: null,
        handleEscape() {
            if (this.armed) {
                this.quickExit();
                return;
            }
            this.armed = true;
            clearTimeout(this.armTimer);
            this.armTimer = setTimeout(() => { this.armed = false; }, 1200);
        },
        quickExit() {
            sessionStorage.clear();
            const leave = () => window.location.replace('{{ $redirectTo }}');
            const token = document.querySelector('meta[name=csrf-token]')?.content;
            if (!token) {
                return leave();
            }
            fetch('{{ $logoutUrl }}', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json' },
                keepalive: true,
            }).catch(() => {}).finally(leave);
        }
    }"
    @keydown.escape.window="handleEscape()"
    class="fixed bottom-4 right-4 z-50">
    <button
        @click="quickExit()"
        type="button"
        class="flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white font-bold py-2.5 px-4 rounded-full shadow-lg transition duration-200 focus:outline-none focus:ring-4 focus:ring-red-300"
        title="Keluar cepat: klik tombol atau tekan Esc dua kali untuk mengakhiri sesi dan pindah ke situs netral">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
            <path fill-rule="evenodd" d="M3 3a1 1 0 00-1 1v12a1 1 0 102 0V4a1 1 0 00-1-1zm10.293 9.293a1 1 0 001.414 1.414l3-3a1 1 0 000-1.414l-3-3a1 1 0 10-1.414 1.414L14.586 9H7a1 1 0 100 2h7.586l-1.293 1.293z" clip-rule="evenodd" />
        </svg>
        <span>Keluar Cepat (ESC ×2)</span>
    </button>
</div>
