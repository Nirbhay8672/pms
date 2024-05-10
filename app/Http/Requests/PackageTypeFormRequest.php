<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PackageTypeFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'name' => 'required|unique:clients,name,' . $this->id,
        ];

        return $rules;
    }

    public function action(): string
    {
        return is_null($this->id) ? 'created' : 'updated';
    }
}