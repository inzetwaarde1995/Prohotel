<?php

namespace App\Http\Requests;

use App\Rules\WebsiteWordfilterRule;
use Illuminate\Foundation\Http\FormRequest;

class UserTagFormRequest extends FormRequest
{
    public const MAX_TAGS = 10;

    protected $errorBag = 'tags';

    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'tag' => is_string($this->input('tag')) ? mb_strtolower(trim($this->input('tag'))) : null,
        ]);
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'tag' => ['required', 'string', 'min:2', 'max:20', 'regex:/^[\pL\pN_-]+$/u', new WebsiteWordfilterRule],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'tag.regex' => __('A tag may only contain letters, numbers, dashes and underscores.'),
        ];
    }
}
