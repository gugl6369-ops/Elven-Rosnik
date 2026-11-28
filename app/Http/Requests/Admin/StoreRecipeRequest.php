<?php

namespace App\Http\Requests\Admin;

use App\Models\Tag;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRecipeRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        if ($this->has('healthy')) {
            $this->merge([
                'healthy' => filter_var($this->input('healthy'), FILTER_VALIDATE_BOOLEAN),
            ]);
        }
    }

    public function authorize(): bool
    {
        return $this->user()?->is_admin === true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:10000'],
            'additional' => ['nullable', 'string', 'max:10000'],
            'advice' => ['nullable', 'string', 'max:10000'],
            'recipe' => ['required', 'string', 'max:50000'],
            'healthy' => ['sometimes', 'boolean'],
            'plant_ids' => ['nullable', 'array'],
            'plant_ids.*' => ['integer', 'exists:plant,id'],
            'tag_ids' => ['nullable', 'array'],
            'tag_ids.*' => ['integer', 'exists:tag,id'],
            'new_tags' => ['nullable', 'array', 'max:50'],
            'new_tags.*.name' => ['required', 'string', 'max:100', 'not_regex:/^\s*$/'],
            'new_tags.*.category' => ['required', 'string', Rule::in(Tag::CATEGORIES)],
        ];
    }
}
