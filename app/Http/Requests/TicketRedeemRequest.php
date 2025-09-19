<?php
// app/Http/Requests/TicketRedeemRequest.php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;

class TicketRedeemRequest extends FormRequest {
  public function authorize(): bool { return true; }
  public function rules(): array {
    return ['qr_hash'=>['required','string','size:64']]; // sha256 hex
  }
}
