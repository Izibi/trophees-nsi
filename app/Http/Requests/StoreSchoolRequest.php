<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSchoolRequest extends FormRequest
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
            'name' => 'required|min:1|max:255',
            'address' => 'required|min:1|max:255',
            'city' => 'required|min:1|max:255',
            'zip' => 'required|min:1|max:255',
            'country_id' => 'required|exists:countries,id',
            'region_id' => 'required|exists:regions,id',
            'uai' => 'required|min:1|max:255',
        ];
    }
}
