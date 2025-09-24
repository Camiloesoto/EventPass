<?php // app/Http/Requests/StoreOrderRequest.php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Enums\OrderStatus;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => ['required','exists:users,id'],
            'order_date' => ['required','date'],
            'status' => ['required', Rule::in(array_column(OrderStatus::cases(), 'value'))],
            'discount_amount' => ['nullable','numeric','min:0'],
            'items' => ['required','array','min:1'],
            'items.*.ticket_type_id' => ['required','exists:ticket_types,id'],
            'items.*.quantity' => ['required','integer','min:1'],
            'items.*.unit_price' => ['required','numeric','min:0'],
        ];
    }
}
