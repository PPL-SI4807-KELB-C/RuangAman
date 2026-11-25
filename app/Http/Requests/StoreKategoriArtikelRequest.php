<?php

namespace App\Http\Requests;

class StoreKategoriArtikelRequest extends KategoriArtikelRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return $this->baseRules();
    }
}
