<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TicketIssueRequest extends FormRequest
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
        ];
    }
}
