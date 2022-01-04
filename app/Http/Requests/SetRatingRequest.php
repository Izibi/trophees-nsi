<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SetRatingRequest extends FormRequest
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
            'score_idea' => 'required|integer|min:1,max:5',
            'score_communication' => 'required|integer|min:1,max:5',
            'score_presentation' => 'required|integer|min:1,max:5',
            'score_image' => 'required|integer|min:1,max:2',
            'score_logic' => 'required|integer|min:1,max:5',
            'score_creativity' => 'required|integer|min:1,max:5',
            'score_organisation' => 'required|integer|min:1,max:5',
            'score_operationality' => 'required|integer|min:1,max:5',
            'score_ouverture' => 'required|integer|min:1,max:3',
        ];
    }
}
