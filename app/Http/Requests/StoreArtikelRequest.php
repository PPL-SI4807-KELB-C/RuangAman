<?php

namespace App\Http\Requests;

class StoreArtikelRequest extends ArtikelRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return $this->baseRules();
    }
}
