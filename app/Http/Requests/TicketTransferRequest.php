<?php

namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;

class TicketTransferRequest extends FormRequest {
  public function authorize(): bool { return true; }
  public function rules(): array {
    return ['to_user_id'=>['nullable','exists:users,id']];
  }
}
