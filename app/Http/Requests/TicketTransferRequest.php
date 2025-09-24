<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TicketTransferRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'to_user_id' => $this->filled('to_user_id') ? (int) $this->input('to_user_id') : null,
            'to_email' => $this->filled('to_email') ? strtolower((string) $this->input('to_email')) : null,
        ]);
    }

    public function rules(): array
    {
        return [
            'to_user_id' => ['nullable', 'integer', 'exists:users,id'],
            'to_email' => ['nullable', 'email', 'exists:users,email'],
        ];
    }
}
