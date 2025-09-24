<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TicketRedeemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'qr_code_hash' => ['required', 'string', function (string $attribute, $value, callable $fail) {
                if (! is_string($value) || ! $this->isValidIdentifier($value)) {
                    $fail('The ticket code or hash is invalid.');
                }
            }],
        ];
    }

    private function isValidIdentifier(string $value): bool
    {
        $value = trim($value);
        if ($value === '') {
            return false;
        }

        if (strlen($value) === 64 && ctype_xdigit($value)) {
            return true;
        }

        $length = strlen($value);
        if ($length >= 10 && $length <= 32 && preg_match('/^[A-Z0-9]+$/i', $value)) {
            return true;
        }

        return false;
    }
}
