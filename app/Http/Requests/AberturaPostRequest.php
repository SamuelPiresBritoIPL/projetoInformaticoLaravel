<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class AberturaPostRequest extends FormRequest
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
        if ($this->isMethod('post')) {
            $rules = [
                'dataAbertura' => ['required', 'date'],
                'dataEncerar' => ['required', 'date','after_or_equal:dataAbertura'],
                'ano' => ['required', 'in:0,1,2,3,4,5'],
                'tipoAbertura' => ['required', 'in:0,1'],
                'semestre' => ['required', 'in:1,2'],
                'idUtilizador' => ['required', 'numeric', Rule::exists('utilizador', 'id')->where('id', $this->request->get('idUtilizador'))],
                'idAnoletivo' => ['required', 'numeric', Rule::exists('anoletivo', 'id')->where('id', $this->request->get('idAnoletivo'))],
            ];
        }else{
            if(request()->has("dataAbertura") || request()->has("dataEncerar")){
                $rules = array_merge($rules,array('dataAbertura' => ['required', 'date'],
                                                'dataEncerar' => ['required', 'date','after_or_equal:dataAbertura']));
            }
            if(request()->has("ano")){
                $rules = array_merge($rules,array('ano' => ['required', 'in:0,1,2,3,4,5']));
            }
            if(request()->has("semestre")){
                $rules = array_merge($rules,array('semestre' => ['required', 'in:1,2']));
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
