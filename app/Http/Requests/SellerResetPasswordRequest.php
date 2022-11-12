<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SellerResetPasswordRequest extends FormRequest
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
            'identity' => ['required', Rule::exists('sellers', 'phone')],
            'token' => ['required'],
            'password' => 'required|same:confirm_password|min:8',
        ];
    }


    protected function prepareForValidation()
    {
        $this->merge(['identity' => validate_phone($this->identity)]);
    }
}
