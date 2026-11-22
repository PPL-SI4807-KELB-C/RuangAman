<!-- Modal Konfirmasi Interaktif (Alpine.js + Tailwind CSS) -->
<div
    x-data="{
        isOpen: false,
        title: 'Konfirmasi Tindakan',
        message: 'Apakah Anda yakin ingin melanjutkan tindakan ini?',
        action: '',
        method: 'POST',
        payload: {},
        confirmText: 'Konfirmasi',
        confirmVariant: 'danger',
        openModal(detail) {
            this.title = detail.title || 'Konfirmasi Tindakan';
            this.message = detail.message || 'Apakah Anda yakin?';
            this.action = detail.action || '';
            this.method = detail.method || 'POST';
            this.payload = detail.payload || {};
            this.confirmText = detail.confirmText || 'Konfirmasi';
            this.confirmVariant = detail.confirmVariant || 'danger';
            this.isOpen = true;
            this.$nextTick(() => {
                this.$refs.confirmButton?.focus();
            });
        },
        closeModal() {
            this.isOpen = false;
        }
    }"
    @open-confirm-modal.window="openModal($event.detail)"
    @keydown.escape.window="if (isOpen) closeModal()"
    x-cloak
    class="relative z-50"
    role="dialog"
    aria-modal="true"
    :aria-hidden="(!isOpen).toString()"
>
    <!-- Backdrop Blur & Overlay -->
    <div
        x-show="isOpen"
        x-transition:enter="ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-neutral-900/50 transition-opacity"
        @click="closeModal()"
    ></div>

    <!-- Modal Box Container -->
    <div class="fixed inset-0 z-10 overflow-y-auto p-4 sm:p-6 lg:p-8" x-show="isOpen">
        <div class="flex min-h-full items-center justify-center text-center sm:p-0">
            <div
                x-show="isOpen"
                x-transition:enter="ease-out duration-250"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-150"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                @click.stop
                class="relative w-full max-w-lg transform overflow-hidden rounded-xl border border-neutral-200/70 bg-white p-6 text-left shadow-lg transition-all sm:my-8"
            >
                <div class="sm:flex sm:items-start gap-4">
                    <!-- Icon Peringatan -->
                    <div
                        class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full sm:mx-0"
                        :class="confirmVariant === 'danger' ? 'bg-safety-rose/10' : 'bg-brand-50'"
                    >
                        <svg
                            class="h-6 w-6"
                            :class="confirmVariant === 'danger' ? 'text-safety-rose' : 'text-brand-700'"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="2"
                            stroke="currentColor"
                            aria-hidden="true"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                        </svg>
                    </div>

                    <!-- Teks Judul & Pesan -->
                    <div class="mt-3 text-center sm:mt-0 sm:text-left flex-1">
                        <h3 class="text-base font-bold leading-6 text-neutral-900" x-text="title"></h3>
                        <div class="mt-2">
                            <p class="text-xs text-neutral-600 leading-relaxed" x-text="message"></p>
                        </div>
                    </div>
                </div>

                <!-- Form Submit Tersembunyi -->
                <form :action="action" method="POST" x-ref="confirmForm" class="hidden">
                    @csrf
                    <input type="hidden" name="_method" :value="method">
                    <template x-for="[key, val] in Object.entries(payload || {})" :key="key">
                        <input type="hidden" :name="key" :value="val">
                    </template>
                </form>

                <!-- Tombol Aksi Modal -->
                <div class="mt-6 flex flex-col-reverse gap-3 border-t border-neutral-200/70 pt-4 sm:flex-row sm:justify-end sm:gap-4">
                    <button
                        type="button"
                        @click="closeModal()"
                        class="inline-flex w-full sm:w-auto items-center justify-center rounded border border-neutral-300/70 bg-white px-5 py-2.5 text-xs font-medium text-neutral-800 transition-colors hover:bg-neutral-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-neutral-900 focus-visible:ring-offset-2"
                    >
                        Batal
                    </button>
                    <button
                        type="button"
                        x-ref="confirmButton"
                        @click="$refs.confirmForm.submit()"
                        class="inline-flex w-full sm:w-auto items-center justify-center rounded px-5 py-2.5 text-xs font-medium text-white transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-neutral-900 focus-visible:ring-offset-2"
                        :class="confirmVariant === 'danger' ? 'bg-safety-rose hover:bg-safety-alert' : 'bg-brand-600 hover:bg-brand-700'"
                        x-text="confirmText"
                    ></button>
                </div>
            </div>
        </div>
    </div>
</div>

