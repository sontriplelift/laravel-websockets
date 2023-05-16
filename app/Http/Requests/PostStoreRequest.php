<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PostStoreRequest extends FormRequest
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
            'title' => ['string', 'required'],
            'body' => ['string', 'required'],
            'user_ids' => ['array', 'required'],
        ];
    }

    public function messages()
    {
        return [
            'body.required' => 'Please enter a value for body.'
        ];
    }
}
