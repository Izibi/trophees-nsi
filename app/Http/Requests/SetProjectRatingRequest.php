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
            //'score_idea' => 'required_if:published,1|nullable|integer|min:0|max:25',
            'score_operationality' => 'nullable|integer|min:0|max:50',
            'score_communication' => 'required_if:published,1|nullable|integer|min:0|max:50',
            'cannot_evaluate_technical' => 'nullable|boolean',
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // When publishing, ensure either technical score is set OR cannot_evaluate_technical is checked
            if ($this->input('published') == 1) {
                $hasTechnicalScore = !is_null($this->input('score_operationality')) && $this->input('score_operationality') !== '';
                $cannotEvaluate = $this->input('cannot_evaluate_technical') == 1 || $this->input('cannot_evaluate_technical') === true;
                
                if (!$hasTechnicalScore && !$cannotEvaluate) {
                    $validator->errors()->add('score_operationality', 'Vous devez soit fournir une note pour les compétences techniques, soit cocher la case indiquant que vous ne pouvez pas les évaluer.');
                }
            }
        });
    }
}
