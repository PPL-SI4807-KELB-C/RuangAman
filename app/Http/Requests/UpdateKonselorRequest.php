<?php

namespace App\Http\Requests;

class UpdateKonselorRequest extends KonselorRequest
{
    /**
     * Akun pengguna (user_id) terkunci setelah profil dibuat agar relasi
     * jadwal dan sesi konseling tetap konsisten.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return $this->baseRules(isUpdate: true);
    }
}
