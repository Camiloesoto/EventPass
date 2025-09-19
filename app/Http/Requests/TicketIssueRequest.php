<?php

namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;

class TicketIssueRequest extends FormRequest {
  public function authorize(): bool { return true; } // gate in controller/policy
  public function rules(): array {
    return ['user_id'=>['nullable','exists:users,id'],'meta'=>['array']];
  }
}
