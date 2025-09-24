<?php // app/Http/Requests/UpdateEventRequest.php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Enums\EventStatus;

class UpdateEventRequest extends FormRequest
{
    public function authorize(): bool 
    { 
        return true; 
    }
    
    public function rules(): array
    {
        return [
            'venue_id' => ['nullable','exists:venues,id'],
            'name' => ['sometimes','string','max:180'],
            'description' => ['sometimes','string'],
            'start_time' => ['sometimes','date','before:end_time'],
            'end_time' => ['sometimes','date','after:start_time'],
            'capacity' => ['sometimes','integer','min:1'],
            'status' => ['sometimes', Rule::in(array_column(EventStatus::cases(), 'value'))],
        ];
    }
}
