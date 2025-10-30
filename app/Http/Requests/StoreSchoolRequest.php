<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Region;

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
        $res = [
            'name' => 'required|max:255',
            'address' => 'required|max:255',
            'city' => 'required|max:255',
            'zip' => 'required|max:255',
            'country_id' => 'required_with:region_id|exists:countries,id',
            'region_id' => 'required|exists:regions,id',
            'uai' => 'nullable|max:255|unique:schools,uai',
            'academy_id' => 'required|nullable|exists:academies,id'
        ];
        if($this->has('region_id')) {
            $region = Region::find($this->get('region_id'));
            if($region && !is_null($region->country_id)) {
                $res['uai'] = 'required|max:255|unique:schools,uai';
            }
        }
        return $res;
    }
    
    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'uai.unique' => 'Un établissement avec ce code UAI existe déjà, veuillez le sélectionner plutôt que de le recréer.',
        ];
    }
}
