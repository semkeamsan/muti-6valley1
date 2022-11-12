<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class VerifyTokenRequest extends FormRequest
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
     * @return array
     */

    public function rules()
    {
        return [
            'phone' => ['required', Rule::exists('users', 'phone')],
            'temporary_token' => ['required', Rule::exists('users', 'temporary_token')]
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge(['phone' => validate_phone($this->phone)]);
    }
}
