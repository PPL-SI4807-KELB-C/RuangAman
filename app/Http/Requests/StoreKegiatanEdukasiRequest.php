<?php

namespace App\Http\Requests;

class StoreKegiatanEdukasiRequest extends KegiatanEdukasiRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return $this->baseRules();
    }
}
