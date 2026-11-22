<?php

namespace App\Http\Requests;

class UpdatePengumumanRequest extends PengumumanRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return $this->baseRules();
    }
}
