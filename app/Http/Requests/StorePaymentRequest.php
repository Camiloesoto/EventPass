<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest; use Illuminate\Validation\Rule; use App\Enums\PaymentStatus;

class StorePaymentRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array
    {
        return [
            'provider' => ['required','string','max:80'],
            'provider_payment_id' => ['required','string','max:191'],
            'amount' => ['required','numeric','min:0'],
            'currency' => ['required','string','size:3'],
            'status' => ['nullable', Rule::in(array_column(PaymentStatus::cases(), 'value'))],
            'paid_at' => ['nullable','date'],
        ];
    }
}
