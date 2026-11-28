<?php

namespace App\Http\Requests\Admin;

use App\Models\Tag;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePlantRequest extends FormRequest
{
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
            'genus_id' => ['nullable', 'integer', 'exists:genus,id', 'required_without:new_genus_name'],
            'new_genus_name' => ['nullable', 'string', 'max:255', 'not_regex:/^\s*$/', 'required_without:genus_id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:10000'],
            'image' => ['nullable', 'image', 'max:5120'],
            'height' => ['nullable', 'numeric', 'min:0'],
            'watering_frequency' => ['nullable', 'string', 'max:255'],
            'sunlight' => ['nullable', 'string', 'max:255'],
            'temp_min' => ['nullable', 'numeric'],
            'temp_max' => ['nullable', 'numeric'],
            'tag_ids' => ['nullable', 'array'],
            'tag_ids.*' => ['integer', 'exists:tag,id'],
            'new_tags' => ['nullable', 'array', 'max:50'],
            'new_tags.*.name' => ['required', 'string', 'max:100', 'not_regex:/^\s*$/'],
            'new_tags.*.category' => ['required', 'string', Rule::in(Tag::CATEGORIES)],
        ];
    }
}
