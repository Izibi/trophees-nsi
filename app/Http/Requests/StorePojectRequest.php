<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePojectRequest extends FormRequest
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
            'name' => 'required|max:255',
            'school_id' => 'required|exists:schools,id',
            'grade_id' => 'exists:grades,id',
            'team_girls' => 'nullable|integer|min:0|max:1000',
            'team_boys' => 'nullable|integer|min:0|max:1000',
            'team_not_provided' => 'nullable|integer|min:0|max:1000',
            'presentation_file' => 'mimes:pdf'
        ];
    }
}
