<?php
namespace App\Http\Requests\Admin;

use App\Enums\TicketStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'order_item_id' => ['required', 'exists:order_items,id'],
            'user_id' => ['nullable', 'exists:users,id'],
            'status' => ['required', Rule::in(array_column(TicketStatus::cases(), 'value'))],
        ];
    }
}
