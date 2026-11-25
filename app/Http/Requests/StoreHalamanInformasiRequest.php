<?php

namespace App\Http\Requests;

class StoreHalamanInformasiRequest extends HalamanInformasiRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return $this->baseRules();
    }
}
