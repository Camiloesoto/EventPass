<?php // app/Http/Requests/StoreEventRequest.php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Enums\EventStatus;

class StoreEventRequest extends FormRequest
{
    public function authorize(): bool 
    { 
        return true; 
    }
    
    public function rules(): array
    {
        return [
            'venue_id' => ['nullable','exists:venues,id'],
            'name' => ['required','string','max:180'],
            'description' => ['required','string'],
            'start_time' => ['required','date','before:end_time'],
            'end_time' => ['required','date','after:start_time'],
            'capacity' => ['required','integer','min:1'],
            'status' => ['required', Rule::in(array_column(EventStatus::cases(), 'value'))],
        ];
    }
}
