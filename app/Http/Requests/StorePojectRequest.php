<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            'school_id' => 'required_if:finalize,1|nullable|exists:schools,id',
            'grade_id' => 'required_if:finalize,1|nullable|exists:grades,id',
            'description' => 'required_if:finalize,1|max:'.config('nsi.project.description_max_length'),
            'teacher_notes' => 'required_if:finalize,1',
            'video' => 'required_if:finalize,1|nullable|url',
            'url' => 'required_if:finalize,1|nullable|url',
            'cb_tested_by_teacher' => 'accepted_if:finalize,1',
            'cb_reglament_accepted' => 'accepted_if:finalize,1',
            'presentation_file' => 'max:'.$conf['presentation_file_size_max'],
            'image_file' => [
                'max:'.$conf['image_file_size_max'],
                Rule::dimensions()->maxWidth($conf['image_max_width'])->maxHeight($conf['image_max_height'])
            ],
            'zip_file' => 'max:'.$conf['zip_file_size_max'],
            'team_member_parental_permissions_file.*' => 'max:'.$conf['parental_permissions_file_size_max']
        ];
    }
}
