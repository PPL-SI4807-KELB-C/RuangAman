<?php

namespace App\Http\Requests;

class UpdateHalamanInformasiRequest extends HalamanInformasiRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return $this->baseRules();
    }
}
