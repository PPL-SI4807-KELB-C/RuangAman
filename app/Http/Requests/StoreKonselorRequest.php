<?php

namespace App\Http\Requests;

class StoreKonselorRequest extends KonselorRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return $this->baseRules();
    }
}
