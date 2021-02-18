<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePostRequest extends FormRequest
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
            'id' => 'required|filled',
            'name' => 'required|max:1024|filled',
            'description' => 'nullable|max:4048',
            'tags' => 'nullable|max:512',
            'extra' => 'nullable|max:4048'
        ];
    }
}
