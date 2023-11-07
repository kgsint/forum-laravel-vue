<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreThreadRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'body' => 'required',
            'topic_id' => 'required|exists:topics,id',
        ];
    }

    public function messages(): array
    {
        return [
            'topic_id.required' => 'Please choose a topic',
            '*.required' => 'The :attribute cannot be empty',
        ];
    }
}
