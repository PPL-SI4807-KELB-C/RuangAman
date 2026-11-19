<?php

namespace App\Http\Requests;

class UpdateArtikelRequest extends ArtikelRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return $this->baseRules();
    }
}
