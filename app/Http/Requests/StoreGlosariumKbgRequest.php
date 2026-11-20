<?php

namespace App\Http\Requests;

class StoreGlosariumKbgRequest extends GlosariumKbgRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return $this->baseRules();
    }
}
