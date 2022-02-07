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
        $conf = config('nsi.project');
        return [
            'name' => 'required|max:255',
            'school_id' => 'required|exists:schools,id',
            'grade_id' => 'required_if:finalize,1|exists:grades,id',
            'team_girls' => 'required_if:finalize,1|nullable|integer|min:0|max:1000',
            'team_boys' => 'required_if:finalize,1|nullable|integer|min:0|max:1000',
            'team_not_provided' => 'required_if:finalize,1|nullable|integer|min:0|max:1000',
            'description' => 'required_if:finalize,1|max:'.config('nsi.project.description_max_length'),
            'video' => 'required_if:finalize,1|nullable|url',
            'cb_tested_by_teacher' => 'accepted_if:finalize,1',
            'presentation_file' => 'size:'.$conf['presentation_file_size_max'],
            'image_file' => 'size:'.$conf['image_file_size_max'],
            'zip_file' => 'size:'.$conf['zip_file_size_max'],
            'parental_permissions_file' => 'size:'.$conf['parental_permissions_file_size_max']
        ];
    }
}
