<?php

namespace App\Http\Requests;

class UpdateKategoriArtikelRequest extends KategoriArtikelRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return $this->baseRules();
    }
}
