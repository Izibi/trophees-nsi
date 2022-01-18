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
            'grade_id' => 'required_if:finalize,1|exists:grades,id',
            'team_girls' => 'required_if:finalize,1|nullable|integer|min:0|max:1000',
            'team_boys' => 'required_if:finalize,1|nullable|integer|min:0|max:1000',
            'team_not_provided' => 'required_if:finalize,1|nullable|integer|min:0|max:1000',
            'description' => 'required_if:finalize,1|max:'.config('nsi.project.description_max_length'),
            'video' => 'required_if:finalize,1|url',
            'cb_tested_by_teacher' => 'accepted_if:finalize,1'
        ];
    }
}
