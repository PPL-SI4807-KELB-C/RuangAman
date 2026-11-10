<?php

namespace App\Http\Requests;

use App\Models\KegiatanEdukasi;
use Illuminate\Validation\Validator;

class UpdateKegiatanEdukasiRequest extends KegiatanEdukasiRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return $this->baseRules();
    }

    public function withValidator(Validator $validator): void
    {
        parent::withValidator($validator);

        $validator->after(function (Validator $validator): void {
            /** @var KegiatanEdukasi|null $kegiatan */
            $kegiatan = $this->route('kegiatan');

            if ($kegiatan && $this->filled('kuota')) {
                $terisi = $kegiatan->terisiKuotaCount();
                if ((int) $this->input('kuota') < $terisi) {
                    $validator->errors()->add(
                        'kuota',
                        "Kuota tidak boleh kurang dari jumlah peserta aktif/hadir saat ini ({$terisi} orang)."
                    );
                }
            }
        });
    }
}
