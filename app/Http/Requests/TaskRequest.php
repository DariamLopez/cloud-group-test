<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;

class TaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        Log::info('Preparing for validation', $this->all());
        if ($this->has('title')){
            $this->merge([
                'title' => is_string($this->title) ? trim($this->title) : $this->title,
            ]);
        }
    }
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'is_done' => ['nullable', 'boolean'],
            'keywords' => ['nullable', 'array', 'max:50'],
            'keywords.*' => ['required','string', 'max:100'],
        ];
    }
    public function messages(): array
    {
        return [
            'title.required' => 'The title field is required.',
            'title.string' => 'The title must be a string.',
            'title.max' => 'The title may not be greater than 255 characters.',
            'is_done.boolean' => 'The is_done field must be true or false.',
            'keywords.array' => 'The keywords must be an array.',
            'keywords.max' => 'The keywords may not have more than 50 items.',
            'keywords.*.required' => 'Each keyword is required.',
            'keywords.*.string' => 'Each keyword must be a string.',
            'keywords.*.max' => 'Each keyword may not be greater than 100 characters.',
        ];
    }

}
