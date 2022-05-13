<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class SetProjectRatingRequest extends FormRequest
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
            'score_idea' => 'required_if:published,1|nullable|integer|min:0,max:5',
            'score_communication' => 'required_if:published,1|nullable|integer|min:0,max:5',
            'score_presentation' => 'required_if:published,1|nullable|integer|min:0,max:5',
            'score_image' => 'required_if:published,1|nullable|integer|min:0,max:2',
            'score_logic' => 'required_if:published,1|nullable|integer|min:0,max:5',
            'score_creativity' => 'required_if:published,1|nullable|integer|min:0,max:5',
            'score_organisation' => 'required_if:published,1|nullable|integer|min:0,max:5',
            'score_operationality' => 'required_if:published,1|nullable|integer|min:0,max:5',
            'score_ouverture' => 'required_if:published,1|nullable|integer|min:0,max:3',
        ];
    }
}
