<?php
namespace App\Http\Requests\Admin;

use App\Enums\TicketStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => ['nullable', 'exists:users,id'],
            'status' => ['required', Rule::in(array_column(TicketStatus::cases(), 'value'))],
        ];
    }
}
