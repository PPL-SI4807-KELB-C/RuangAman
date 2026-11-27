{{-- Modal Hapus FAQ (Figma Exact) --}}
<div x-show="deleteModalOpen"
     x-transition.opacity
     class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-[#1C1917]/30"
     role="dialog"
     aria-modal="true"
     aria-labelledby="modal-hapus-faq-title"
     x-cloak>
    <div class="bg-[#FAF9F7] rounded-[12px] w-full max-w-[560px] p-8 shadow-xl relative"
         @click.away="closeDeleteModal()"
         @keydown.escape.window="closeDeleteModal()">
        <h2 id="modal-hapus-faq-title" class="text-[26px] font-medium text-[#1C1917] leading-tight">
            Hapus FAQ ini?
        </h2>
        <div class="text-[18px] font-medium text-[#1C1917] mt-4 leading-snug" x-text="deletePertanyaan"></div>
        <p class="text-base text-[#686563] font-normal mt-4 leading-relaxed">
            FAQ akan dihapus dari daftar admin dan tidak lagi ditampilkan pada pusat bantuan publik.
        </p>

        <div class="bg-[#EDECE9] rounded-[6px] p-4 text-[13px] text-[#686563] mt-6 leading-relaxed">
            Tindakan ini tidak dapat dibatalkan. Untuk menyembunyikan FAQ sementara, ubah statusnya menjadi Draf.
        </div>

        <div class="flex items-center justify-end gap-3 mt-8">
            <button type="button"
                    @click="closeDeleteModal()"
                    class="bg-[#EDECE9] hover:bg-[#e4e2de] text-[#1C1917] text-sm font-medium rounded-[2px] px-7 py-3 transition">
                Batal
            </button>
            <form :action="deleteActionUrl" method="POST" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="bg-[#B42318] hover:bg-[#991b1b] text-white text-sm font-medium rounded-[2px] px-7 py-3 transition">
                    Ya, hapus FAQ
                </button>
            </form>
        </div>
    </div>
</div>
