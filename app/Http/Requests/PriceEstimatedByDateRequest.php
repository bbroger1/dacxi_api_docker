<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PriceEstimatedByDateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'date' => 'required',
            'time' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'date.required' => 'The date (dd-mm-YYYY) is required',
            'time.required' => 'The time (HH:ii) is required'
        ];
    }
}
