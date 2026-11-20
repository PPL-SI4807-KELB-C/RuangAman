<?php

namespace App\Http\Requests;

class StorePengumumanRequest extends PengumumanRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return $this->baseRules();
    }
}
