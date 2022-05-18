<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class TurnoPostRequest extends FormRequest
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
        $rules = [];
        if($this->isMethod('put')){
            if($this->request->has('visivel')){
                $rules = array_merge($rules,array('visivel' => 'required', 'numeric', 'in:0,1'));
            }
            if($this->request->has('repetentes')){
                $rules = array_merge($rules,array('repetentes' => 'required', 'numeric', 'in:0,1'));
            }
            if($this->request->has('vagastotal')){
                $rules = array_merge($rules,array('vagastotal' => 'required|numeric|gt:0'));
            }
        }
        return $rules;
    }

    /**
    * [failedValidation [Overriding the event validator for custom error response]]
    * @param  Validator $validator [description]
    * @return [object][object of various validation errors]
    */
    public function failedValidation(Validator $validator) { 
        //write your bussiness logic here otherwise it will give same old JSON response
       throw new HttpResponseException(response()->json($validator->errors(), 422)); 
   }
}
