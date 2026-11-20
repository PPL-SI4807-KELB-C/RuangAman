<?php

namespace App\Http\Requests;

class UpdateGlosariumKbgRequest extends GlosariumKbgRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return $this->baseRules();
    }
}
